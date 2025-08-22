# Regras por Endpoint e Fluxos — Projeto RPG (Laravel + Filament)

> **Base**: segue os conceitos e estrutura do documento “Rpg – Especificação MVP (Laravel + Filament)”.  
> **Objetivo**: detalhar **quem pode fazer o quê**, **validações passo a passo**, **regras de negócio** e **cálculos** para cada endpoint/funcionalidade do MVP.

---

## 0) Convenções Gerais

### Papéis e Escopo
- **Papéis por Mundo**: `admin`, `mestre`, `jogador` (tabela `usuarios_mundos`).  
- **Escopo**: toda ação que lê/escreve entidades escopadas por mundo **deve** filtrar/validar `mundo_id`.  
- **Ownership**: um **jogador** pode alterar apenas seus próprios Personagens; **mestre/admin** podem visualizar/ajustar Personagens do mundo (com auditoria).

### Middlewares
- `auth:jwt`: valida o token e popula `auth()->user()`.
- `scope.mundo`: resolve `mundo_id` ativo pela rota/header/query e valida se o usuário tem vínculo no mundo.
- `role:*`: checa papel por mundo com base em `usuarios_mundos`.

### Erros Padrão
- `401 Unauthorized`: usuário não autenticado.
- `403 Forbidden`: usuário autenticado sem permissão no mundo/recurso.
- `404 Not Found`: recurso não existe **ou** não pertence ao `mundo_id` ativo.
- `409 Conflict`: violação de regra de negócio (ex.: pontos excedidos).
- `422 Unprocessable Entity`: validação de payload.
- `500 Internal Server Error`: exceções não tratadas.

### Transações e Auditoria
- **Transação**: operações de escrita envolvendo múltiplas tabelas **devem** usar `DB::transaction(...)`.
- **Auditoria**: tabela `auditoria(evento, usuario_id, mundo_id, payload_before, payload_after, criado_em)`.

### Configurações de Regras do Mundo
Para suportar X/Y e limites, recomenda-se `mundos_regras`:
```sql
CREATE TABLE mundos_regras (
  id SERIAL PRIMARY KEY,
  mundo_id INTEGER NOT NULL REFERENCES mundos(id) ON DELETE CASCADE,
  pontos_base_por_personagem INT DEFAULT 0, -- X
  niveis_dado_por_personagem INT DEFAULT 0, -- Y
  sequencia_dados INT[] DEFAULT '{4,6,8,10,12,20}', -- faces ordenadas
  limite_max_tipo_dado_id INTEGER REFERENCES tipos_dado(id), -- opcional
  permite_pvp BOOLEAN DEFAULT FALSE,
  permite_pve BOOLEAN DEFAULT TRUE,
  UNIQUE (mundo_id)
);
```
> Observação: se esta tabela **não** existir ainda, endpoints que dependem de X/Y devem exigir os valores no payload da criação de personagem.

---

## 1) Autenticação

### POST /auth/register
**Quem pode:** público.  
**Payload:** `{ nome, email, senha }`  
**Validações:**
1. `nome` 1..255, `email` único, `senha` >= 8.
2. Hash com `Password::hash`.
**Resultado:** `201` com `{id, nome, email}` (sem senha).

### POST /auth/login
**Quem pode:** público.  
**Payload:** `{ email, senha }`  
**Validações:**
1. Verifica usuário por email; valida hash de senha.
2. Emite JWT com `sub`, `email`, `nome`.
**Resultado:** `200` `{ token, user }`.

### GET /auth/me
**Quem pode:** autenticado.  
**Regras:** retorna dados do usuário e lista de mundos e papéis.

---

## 2) Mundos

### POST /mundos
**Quem pode:** autenticado.  
**Regra:** criador vira `admin` do mundo.  
**Payload:** `{ nome, descricao? }`  
**Passo a passo:**
1. Valida `nome` único global ou apenas informativo (recomendado: único por usuário).
2. `INSERT` em `mundos` (criado_por = user.id).
3. `INSERT` em `usuarios_mundos` com papel `admin`.
4. (Opcional) `INSERT` em `mundos_regras` com padrões X/Y.
**Resposta:** `201 { mundo }`.

### GET /mundos
**Quem pode:** autenticado.  
**Regra:** lista apenas mundos onde o usuário tem vínculo.

### PATCH /mundos/{id} | DELETE /mundos/{id}
**Quem pode:** `admin` do mundo.  
**Validações:** existência e escopo; não permitir apagar se houver dependências (ou `ON DELETE CASCADE` cuidadosamente).

### POST /mundos/{id}/membros
**Quem pode:** `admin`.  
**Payload:** `{ usuario_id, papel }`  
**Regras:**
1. `papel ∈ {admin,mestre,jogador}`.
2. `UNIQUE(usuario_id, mundo_id, papel)`.
3. Não remover o **último** `admin`.

### GET/PUT /mundos/{id}/regras
**Quem pode:** `admin`/`mestre` (PUT apenas `admin`).  
**Campos:** `pontos_base_por_personagem (X)`, `niveis_dado_por_personagem (Y)`, `sequencia_dados`, `limite_max_tipo_dado_id`, `permite_pvp`, `permite_pve`.

---

## 3) Campanhas

### POST /mundos/{mundo}/campanhas
**Quem pode:** `admin`/`mestre`.  
**Payload:** `{ nome, descricao?, data_inicio?, data_fim? }`  
**Regras:** datas válidas (fim >= início).

### GET /mundos/{mundo}/campanhas[/{id}]
**Quem pode:** qualquer membro do mundo.  
**Regra:** filtrar por `mundo_id`.

### PATCH/DELETE /mundos/{mundo}/campanhas/{id}
**Quem pode:** `admin`/`mestre`.

---

## 4) Atributos

### POST /mundos/{mundo}/atributos
**Quem pode:** `admin`/`mestre`.  
**Payload:** `{ chave, nome, descricao?, exibir? }`  
**Validações:**
1. `UNIQUE(mundo_id, chave)`.
2. `chave` slug-like `[a-z0-9_]+`.
3. `nome` 1..255.

### GET/PATCH/DELETE idem
**Quem pode:** leitura por qualquer membro; escrita por `admin`/`mestre`.  
**Regra:** negar DELETE se houver dependências (`classes_atributos`, `origens_efeitos`, personagens).

---

## 5) Classes e Regras por Atributo

### POST /mundos/{mundo}/classes
**Quem pode:** `admin`/`mestre`.  
**Payload:** `{ slug, nome, descricao? }`  
**Validações:** `UNIQUE (mundo_id, slug)`.

### POST /mundos/{mundo}/classes/{classe}/atributos
**Quem pode:** `admin`/`mestre`.  
**Payload por atributo:**  
```
{
  atributo_id,
  tipo_dado_id?,     // dado inicial
  base_fixa = 0,
  limite_base_fixa?, // se null => sem limite
  limite_tipo_dado_id?, // se null => sem limite
  imutavel = true
}
```
**Passo a passo:**
1. Verifica se `atributo_id` pertence ao mesmo `mundo`.
2. Verifica `UNIQUE(classe_id, atributo_id)`.
3. Se `limite_tipo_dado_id` informado, valida que é >= `tipo_dado_id` na sequência do mundo.
4. `INSERT` em `classes_atributos`.

### PATCH/DELETE classe/atributo
- **PATCH**: somente `admin`/`mestre`. Se já existir Personagem com a classe, mudanças críticas **devem** gerar entrada de auditoria e recalcular personagens impactados (ou bloquear alterações que quebrem personagens).
- **DELETE**: negar se houver personagens usando a classe.

### GET (listar classes e atributos da classe)
**Quem pode:** qualquer membro do mundo.

---

## 6) Origens e Efeitos

### POST /mundos/{mundo}/origens
**Quem pode:** `admin`/`mestre`.  
**Payload:** `{ slug, nome, descricao? }`  
**Validações:** `UNIQUE (mundo_id, slug)`.

### POST /mundos/{mundo}/origens/{origem}/efeitos
**Quem pode:** `admin`/`mestre`.  
**Payload (um por registro):**
```
{
  tipo: 'delta_atributo'|'conceder_item'|'conceder_habilidade'|'custom',
  atributo_id?,   // requerido se tipo == delta_atributo
  delta?,         // int; pode ser negativo
  notas?          // JSON para itens/habilidades/custom
}
```
**Regra:** se `tipo == delta_atributo` então `atributo_id` e `delta` são obrigatórios.

### GET/PATCH/DELETE origens e efeitos
- Leitura: qualquer membro.
- Escrita: `admin`/`mestre` (DELETE negado se houver personagens vinculados, a menos que haja migração).

---

## 7) Habilidades

### POST /mundos/{mundo}/habilidades
**Quem pode:** `admin`/`mestre`.  
**Payload:** `{ slug, nome, descricao?, bonus? }`  
**Validações:**
- `bonus` é um JSON do tipo `{ "<chave_atributo_ou_regra>": number }`. Ex.: `{ "forca": 2, "amizade": -1 }`.

### Vincular habilidade à Classe/Origem
- **POST /classes/{classe}/habilidades/{habilidade}**
- **POST /origens/{origem}/habilidades/{habilidade}**
**Quem pode:** `admin`/`mestre`.  
**Regra:** `UNIQUE` no vínculo. Mesma `mundo_id` dos 2 lados.

### GET/PATCH/DELETE
- Leitura: membros do mundo.
- Escrita: `admin`/`mestre`.

---

## 8) Itens e Armas

### POST /mundos/{mundo}/itens
**Quem pode:** `admin`/`mestre`.  
**Payload:** `{ slug, nome, tipo, descricao?, dados_dano?, propriedades? }`  
**Validações:**
- `tipo ∈ { arma, armadura, consumivel, acessorio, outro }`.
- `dados_dano` (se presente) deve casar com regex `^\d+d(4|6|8|10|12|20)$`.
- `propriedades` JSON livre.

### GET/PATCH/DELETE
- Leitura: membros.
- Escrita: `admin`/`mestre`.
- DELETE: negar se item estiver em inventários de personagens/NPCs (ou remover com cascata explícita).

---

## 9) NPCs

### POST /mundos/{mundo}/npcs
**Quem pode:** `admin`/`mestre`.  
**Payload:** `{ nome, descricao?, classe_id?, origem_id?, atributos?, inventario? }`  
**Validações:**
1. Se `classe_id`/`origem_id` presentes, devem pertencer ao `mundo`.
2. `atributos` (JSON) pode exceder limites (NPCs são flexíveis).
3. `inventario` JSON livre.

### GET/PATCH/DELETE
- Leitura: membros.
- Escrita: `admin`/`mestre`.

---

## 10) Personagens

### Definições (importante)
- **X = pontos_base_por_personagem** (de `mundos_regras`) OU limite_base_fixa de (`classes_atributos`).  
- **Y = niveis_dado_por_personagem** (de `mundos_regras`) OU limite_base_fixa de (`limite_tipo_dado_id`).  
- **Distribuição de X**: deve ser **por atributo**. Opcionalmente armazenar em `personagens.pontos_base_map JSONB` (sugestão) no formato `{ atributo_id: pontos }`.  
  - Se o campo não existir no banco, **exigir a distribuição no payload** e calcular **on-the-fly** (ou persistir em `atributos_override` com uma chave distinta `base_alloc`, não recomendado).

### POST /mundos/{mundo}/personagens
**Quem pode:** `jogador` (criador) e também `mestre/admin` (criar para um jogador).  
**Payload (exemplo):**
```json
{
  "usuario_id": 123,            // opcional; default = auth.id (apenas mestre/admin pode criar para outro)
  "campanha_id": 45,            // opcional
  "classe_id": 7,
  "origem_id": 9,               // opcional
  "nome": "Thorga",
  "pontos_base_map": { "1": 2, "2": 1 },   // atributo_id -> pontos
  "niveis_dado":     { "1": 1, "3": 2 },   // atributo_id -> níveis (inteiros)
  "inventario": { "moedas": 10 }
}

// Exemplo mais elaborado

{
  "usuario_id": 123,
  "campanha_id": 45,
  "classe_id": 7,
  "origem_id": 9,
  "nome": "Thorga",
  "pontos_base_map": {
    "1": 2,   // Força
    "2": 1    // Destreza
  },
  "niveis_dado": {
    "1": 1,   // Força -> evolui d4 -> d6
    "3": 2    // Amizade -> evolui d4 -> d8
  },
  "inventario": {
    "moedas": 10,
    "arma_inicial": "machado_duas_maos"
  }
}

```
**Validações (passo a passo):**
1. Usuário tem vínculo no `mundo` **e** (se `usuario_id` especificado) criador é `mestre/admin` ou `usuario_id == auth.id`.
2. `classe_id`, `origem_id?`, `campanha_id?` pertencem ao `mundo`.
3. Carrega `X` e `Y` de `mundos_regras` ou `classes_atributos`. Se ausente, exige `X/Y` no payload e usa-os **somente nesta criação**.
4. Carrega `classes_atributos` da `classe_id`:
   - Se `imutavel = true` num atributo, **proibir** distribuição em `niveis_dado[atrib]` e **ignorar** `pontos_base_map[atrib]` (ou aplicar somente se regra permitir).
5. **Soma de pontos base**: `sum(pontos_base_map.values) <= X`. Caso exceda ⇒ `409 Conflict`.
6. **Soma de níveis de dado**: `sum(niveis_dado.values) <= Y`. Caso exceda ⇒ `409`.
7. Para cada `atributo_id`:
   - `p_base = pontos_base_map.get(atrib, 0)`.
   - `nivel = niveis_dado.get(atrib, 0)`.
   - **Tipo de dado inicial** = `classes_atributos.tipo_dado_id` (pode ser null ⇒ usar mínimo da sequência do mundo).
   - **Evolui dado** `nivel` passos na `sequencia_dados` (ver *Cálculos*).  
   - **Aplica limites**: se `limite_tipo_dado_id` presente, trunca evolução nesse limite; se `limite_base_fixa` presente, `p_base` não pode ultrapassar `(limite_base_fixa - base_fixa)`.
8. **Origem** (se houver): somar deltas de `origens_efeitos` por atributo.
9. **Habilidades**: reunir `habilidades` da `classe` e da `origem` e somar `bonus` por atributo (se houver).
10. **Valor efetivo preliminar** por atributo =  
    `base_fixa (classe)` + `p_base` + `delta_origem` + `bonus_habilidades`.
11. **Override do mestre** (se presente em `atributos_override[atrib]`): substitui o valor final.

**Persistência:**
- `INSERT` em `personagens` (`niveis_dado`, `pontos_base_map` se existir coluna, `inventario`...).  
- (Opcional) `INSERT` em `auditoria` com snapshot.

**Resposta:** `201 { personagem_id, ... }`

### GET /mundos/{mundo}/personagens[/{id}]
**Quem pode:** `jogador` (somente seus), `mestre/admin` (todos no mundo).  
**Regra:** se `?com_calculo=1`, retorna além dos dados: **atributos calculados** e **dados atualizados**.

### PATCH /mundos/{mundo}/personagens/{id}
**Quem pode:** dono do personagem (ajustes próprios **sem** override) **ou** `mestre/admin` (inclusive override).  
**Payload (parcial):** `{ nome?, campanha_id?, pontos_base_map?, niveis_dado?, atributos_override?, inventario? }`  
**Validações:** iguais à criação, porém:
- A soma total de `pontos_base_map` **não pode exceder X** do mundo.
- A soma total de `niveis_dado` **não pode exceder Y** do mundo.
- Se algum atributo da classe é `imutavel`, negar alterações nos campos correspondentes.
- Recalcular e auditar diferenças.

### POST /mundos/{mundo}/personagens/{id}/reset-alocacao
**Quem pode:** `mestre/admin`.  
**Efeito:** zera `pontos_base_map` e/ou `niveis_dado` (mantém inventário e histórico).

### POST /mundos/{mundo}/personagens/{id}/equipar-item
**Quem pode:** dono **ou** `mestre/admin`.  
**Payload:** `{ item_id, quantidade? }`  
**Validações:** `item_id` do `mundo`, quantidade >= 1. Atualiza `inventario` (JSON).

---

## 11) Cálculos (detalhes)

### Sequência de Dados e Evolução
- Obter `sequencia_dados` do mundo (faces ordenadas). Ex.: `[4,6,8,10,12,20]`.
- Mapear `tipos_dado` para faces (`id -> faces`).

**Função `evoluirDado(dadoInicialId, nivel, limiteTipoId?, limiteMaxTipoId?)`:**
1. `facesInicial = tipos_dado[dadoInicialId].faces` (se null ⇒ menor face da sequência).
2. `idx = indexOf(sequencia_dados, facesInicial)`.
3. `idxFinal = min(idx + nivel, idxLimites)` onde `idxLimites` é o menor índice equivalente a `limiteTipoId` ou `limiteMaxTipoId` (se existirem).
4. `facesFinal = sequencia_dados[idxFinal]`.
5. **Resultado:** `tipo_dado_final_id` (lookup por faces).

### Valor Efetivo de Atributo
Para cada `atrib`:
```
valor = base_fixa_classe(atrib)
      + pontos_base_map.get(atrib, 0)
      + delta_origem(atrib)
      + bonus_habilidades(atrib)
if override := atributos_override.get(atrib):
    valor = override
```
- `delta_origem(atrib)` = soma de `origens_efeitos` do tipo `delta_atributo` para o `atrib`.
- `bonus_habilidades(atrib)` = soma dos valores numéricos presentes em `habilidades.bonus[<chave_do_atributo>]` provenientes de classe/origem (e no futuro, do personagem).

### Valor Esperado do Dado (opcional para prévias)
- Para um dado com `N` faces: `E = (1 + N) / 2`.  
- Pode ser retornado como `atributos_previstos[atrib]` quando `?preview=1`.

---

## 12) Exemplos de Fluxos (Query Builder)

### Criar Personagem (trecho)
```php
DB::transaction(function() use ($payload, $user, $mundoId) {
    $regras = DB::table('mundos_regras')->where('mundo_id', $mundoId)->first();
    $X = $regras->pontos_base_por_personagem ?? $payload['X'] ?? 0;
    $Y = $regras->niveis_dado_por_personagem ?? $payload['Y'] ?? 0;

    $classeAttrs = DB::table('classes_atributos')
        ->where('classe_id', $payload['classe_id'])
        ->join('atributos', 'atributos.id', '=', 'classes_atributos.atributo_id')
        ->select('classes_atributos.*', 'atributos.chave')
        ->get();

    // Valida X/Y, limites, imutáveis, etc. (ver regras acima)
    // ...

    $id = DB::table('personagens')->insertGetId([
        'mundo_id' => $mundoId,
        'usuario_id' => $payload['usuario_id'] ?? $user->id,
        'campanha_id' => $payload['campanha_id'] ?? null,
        'classe_id' => $payload['classe_id'],
        'origem_id' => $payload['origem_id'] ?? null,
        'pontos_base' => array_sum($payload['pontos_base_map'] ?? []), // total
        'niveis_dado' => json_encode($payload['niveis_dado'] ?? new \stdClass()),
        'atributos_override' => json_encode($payload['atributos_override'] ?? new \stdClass()),
        'inventario' => json_encode($payload['inventario'] ?? new \stdClass()),
        'criado_em' => now(),
    ]);

    DB::table('auditoria')->insert([
        'evento' => 'CRIAR_PERSONAGEM',
        'usuario_id' => $user->id,
        'mundo_id' => $mundoId,
        'payload_before' => null,
        'payload_after' => json_encode(['personagem_id' => $id] + $payload),
        'criado_em' => now()
    ]);
});
```

---

## 13) Segurança e Rate-Limiting

- **Rate limiting**: `/auth/*` e `/personagens/*` com cotas conservadoras (ex.: 60 req/min por IP + user).
- **Data masking**: nunca retornar `senha_hash`.
- **Autorização forte**: toda mutação verifica `usuarios_mundos`.
- **Auditoria**: toda ação de mestre/admin em personagens de terceiros gera trilha.

---

## 14) Resumo (quem pode o quê)

- **Admin**: tudo no mundo, inclusive regras, papéis, exclusões.
- **Mestre**: tudo de campanha, atributos, classes, origens, habilidades, itens, NPCs; criar/ajustar personagens (com override).
- **Jogador**: criar/ler/editar **apenas** seus personagens (sem override), listar catálogos (read-only).

---

## 15) Pendências/Roadmap

- Testes e rolagens (PVP/PVE) com oponente “se defender” por atributos.
- Logs detalhados por sessão de campanha.
- Sistema de perícias/proficiências (se aplicável).
- Vínculos de habilidades obtidas por nível/classe/origem ao longo da progressão.
- Migrações seguras quando classe/atributos/limites mudarem (jobs assíncronos + auditoria).
