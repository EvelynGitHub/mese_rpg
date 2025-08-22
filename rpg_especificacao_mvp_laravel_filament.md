# Projeto RPG de Mesa — Especificação Técnica (MVP e Roadmap)

> **Stack**: Laravel (sem Eloquent no domínio — apenas Query Builder `DB`), Filament (web/admin; Eloquent somente como *read/write models* para UI), PostgreSQL (preferência) / MySQL (alternativo), JWT, API-first; React Native (Expo) na fase 2.

> **Objetivo**: Plataforma para apoiar mesas de RPG, permitindo que Mestres configurem **Mundos/Campanhas**, **Atributos dinâmicos**, **Classes**, **Origens**, **Habilidades**, **Itens/Armas**, **NPCs** e que Jogadores criem **Personagens** com distribuição de pontos e progressão de dados (d4→d6→d8…), com cálculos automáticos e trilhas de auditoria. Suporte a PVP/PVE em roadmap.

---

## 1. Visão Geral

- **Mundo** é o escopo de isolamento: nada vaza entre mundos. Cada Mundo pode ter múltiplos Mestres e múltiplas Campanhas.
- **Campanha** organiza sessões/jogos dentro de um Mundo.
- **Usuários** podem atuar como **Mestre** e/ou **Jogador** em um ou mais Mundos.
- **Atributos** são definidos livremente pelo Mestre por Mundo (ex.: Vida, Mana, Amizade, Destreza, Força…).
- **Classes** definem dados fixos, bases por atributo e limites opcionais (ex.: Bárbaro: Força d20 + base 2, limite de evolução d20; Amizade d4 + base 0 — imutáveis na classe)..
- **Distribuição de pontos** pelo Jogador: `X` pontos base e `Y` níveis de dado por atributo (cada nível evolui o dado: d4→d6→d8→d10→d12→d20, limite por Mundo/Regra/Classe).
- **Origem** aplica efeitos (passivos/ativos), bônus/malus em atributos, armas, líderes, habilidades iniciais, etc.
- **NPCs**, **Habilidades**, **Itens/Armas**: catálogos por Mundo com vínculos a Classes/Origens/Personagens.
- **Cálculo automático**: valores efetivos por atributo (classe base + alocação + efeitos de origem + progressão de dado [+ override do Mestre]).
- **Overrides do Mestre**: ajuste manual em qualquer momento, com histórico.
- **API-first**: o backend expõe endpoints consumidos pelo Filament (admin) e por futuros apps (React Native).

---

## 2. Decisões de Arquitetura

- **Laravel**: Controladores, middlewares, validação, autenticação (JWT custom), roteamento, migrations.
- **Sem Eloquent no Domínio**: Casos de Uso/Repositórios usam `DB::table(...)` (Query Builder).
- **Filament**: usado como painel/admin **somente**; criaremos *Eloquent Read/Write Models* simples **apenas** para as `Resources` (UI). O domínio (regras de negócio) não depende de Eloquent.
- **Módulos (DDD/Clean)**: pastas por contexto (`Users`, `Mundos`, `Campanhas`, `Atributos`, `Classes`, `Origens`, `Habilidades`, `Itens`, `NPCs`, `Personagens`, `Combate`).
- **Escopo de Mundo**: todas as entidades relevantes possuem `mundo_id`. Middlewares aplicam **filtro por mundo** em cada requisição.
- **Autenticação**: JWT (HS256). Middleware `auth:jwt`. Payload inclui `sub`, `email`, `nome`, papéis e (opcional) `mundo_id` ativo.
- **Autorização**: tabela `usuarios_mundos` com papeis (`admin`, `mestre`, `jogador`). Policies por mundo.

Estrutura sugerida (resumo):

```
app/
  Modules/
    Users/ ...
    Mundos/ ...
    Campanhas/ ...
    Atributos/ ...
    Classes/ ...
    Origens/ ...
    Habilidades/ ...
    Itens/ ...
    NPCs/ ...
    Personagens/ ...
    Combate/ ...
app/Http/Controllers/* (finos; chamam casos de uso)
app/Domain/* (Entidades simples/DTOs)
app/UseCases/* (orquestram regras)
app/Repositories/* (Query Builder)
app/Filament/* (Resources; Eloquent-only models p/ UI)
app/Http/Middleware/ScopeMundo.php
routes/api.php
```

---

## 3. MVP — Escopo e Prioridades

1. **Autenticação**: registro, login, `me`, refresh opcional; JWT; perfis por Mundo.
2. **Mundos**: CRUD; vínculo de usuários e papéis por mundo (`usuarios_mundos`).
3. **Campanhas**: CRUD dentro do Mundo; relação com Mestres e Jogadores.
4. **Atributos Dinâmicos** por Mundo: CRUD; marcações úteis (ex.: se participa de rolagem, exibição, limites).
5. **Classes** por Mundo: CRUD; definição de dado, base por atributo e limites de evolução; imutáveis na classe.
6. **Origens** por Mundo: CRUD; efeitos (delta de atributo, conceder item/habilidade, custom).
7. **Habilidades** por Mundo: CRUD; vínculos com Classe/Origem/Personagem, suporte a bônus/malus.
8. **Itens/Armas** por Mundo: CRUD; tipos, dano, requisitos, raridade.
9. **NPCs** por Mundo: CRUD; com atributos opcionais, inventário simplificado, vínculo opcional a Classe e Origem.
10. **Personagens**: criação (classe+origem), distribuição de **pontos base (X)** e **níveis de dado (Y)** por atributo; cálculo efetivo; overrides do Mestre; inventário básico; vínculos a campanha, usuário e mundo.
11. **Histórico/Auditoria**: grava alterações relevantes.

> **Calculadora**: valores efetivos de atributo = `baseClasse` + `pontosBasePersonagem` + `deltaOrigem` + `bonusItem/Habilidade` + `valorDoDado(dadoAtual)` (ou **valor esperado** do dado p/ prévia) — com **override** do Mestre predominando.

---

## 4. Regras de Negócio (essenciais)

- **Progressão de Dados**: cada ponto em "nível de dado" evolui o dado do atributo seguindo a sequência definida no Mundo (padrão: d4→d6→d8→d10→d12→d20). Limite configurável por Mundo (ex.: até d20).
- **Classe**: para cada atributo, define **dado inicial**, **base fixa** e limites opcionais (limite_base_fixa, limite_tipo_dado_id) podendo ter dados imutáveis na classe. Ex.: Bárbaro: Força d20 + 2 base; Amizade d4 + 0.
- **Origem**: aplica **efeitos** ao criar o personagem: deltas de atributos, habilidades, itens, ou efeito custom.
- **Pontos de Personagem**: Mestre define X (pontos base) e Y (níveis de dado) disponívies. Jogador distribui respeitando limites e validações do Mundo e da Classe.
- **Habilidades**: podem conter **bonus/malus** descritos em JSONB, aplicáveis a atributos ou efeitos especiais.
- **NPCs**: podem ter atributos customizados, inventário, além de vínculo a Classe e Origem.
- **Personagens**: possuem vínculo a Classe, Origem, Usuário e Campanha, além de pontos_base, niveis_dado, atributos_override e inventário.
- **Overrides do Mestre**: qualquer valor calculado pode ser substituído por valor fixo; tudo é auditado.
- **Escopo**: todas as listagens/consultas devem filtrar por `mundo_id` ativo.

---

## 5. Modelagem de Dados (PostgreSQL) — em Português

> **Observação**: nomes PT-BR; traduza para MySQL mantendo tipos equivalentes. Ajuste `TIMESTAMPTZ` para `TIMESTAMP` se necessário.

### 5.1 Catálogos base

```sql
CREATE TABLE tipos_dado (
  id SERIAL PRIMARY KEY,
  codigo VARCHAR(10) UNIQUE NOT NULL, -- 'd4','d6','d8','d10','d12','d20'
  faces INT NOT NULL
);

INSERT INTO tipos_dado (codigo, faces) VALUES
 ('d4',4),('d6',6),('d8',8),('d10',10),('d12',12),('d20',20);
```

### 5.2 Usuários e Autorização por Mundo

```sql
CREATE TABLE usuarios (
  id SERIAL PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  senha_hash VARCHAR(255) NOT NULL,
  criado_em TIMESTAMPTZ DEFAULT now(),
  atualizado_em TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE mundos (
  id SERIAL PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  descricao TEXT,
  criado_por INTEGER REFERENCES usuarios(id),
  criado_em TIMESTAMPTZ DEFAULT now()
);

-- Papel por mundo: admin, mestre, jogador
CREATE TYPE papel_mundo AS ENUM ('admin','mestre','jogador');
CREATE TABLE usuarios_mundos (
  id SERIAL PRIMARY KEY,
  usuario_id INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
  mundo_id INTEGER NOT NULL REFERENCES mundos(id) ON DELETE CASCADE,
  papel papel_mundo NOT NULL,
  UNIQUE(usuario_id, mundo_id, papel)
);
```

### 5.3 Campanhas e Sessões

```sql
CREATE TABLE campanhas (
  id SERIAL PRIMARY KEY,
  mundo_id INTEGER NOT NULL REFERENCES mundos(id) ON DELETE CASCADE,
  nome VARCHAR(255) NOT NULL,
  descricao TEXT,
  data_inicio DATE,
  data_fim DATE,
  criado_por INTEGER REFERENCES usuarios(id),
  criado_em TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE sessoes (
  id SERIAL PRIMARY KEY,
  campanha_id INTEGER NOT NULL REFERENCES campanhas(id) ON DELETE CASCADE,
  data_hora TIMESTAMPTZ NOT NULL,
  resumo TEXT
);
```

### 5.4 Atributos Dinâmicos

```sql
CREATE TABLE atributos (
  id SERIAL PRIMARY KEY,
  mundo_id INTEGER NOT NULL REFERENCES mundos(id) ON DELETE CASCADE,
  chave VARCHAR(100) NOT NULL,  -- ex.: 'forca', 'amizade'
  nome VARCHAR(255) NOT NULL,
  descricao TEXT,
  exibir BOOLEAN DEFAULT TRUE,
  UNIQUE (mundo_id, chave)
);
```

### 5.5 Classes e Regras por Atributo

```sql
CREATE TABLE classes (
  id SERIAL PRIMARY KEY,
  mundo_id INTEGER NOT NULL REFERENCES mundos(id) ON DELETE CASCADE,
  slug VARCHAR(120) NOT NULL,
  nome VARCHAR(255) NOT NULL,
  descricao TEXT,
  UNIQUE (mundo_id, slug)
);

CREATE TABLE classes_atributos (
  id SERIAL PRIMARY KEY,
  classe_id INTEGER NOT NULL REFERENCES classes(id) ON DELETE CASCADE,
  atributo_id INTEGER NOT NULL REFERENCES atributos(id) ON DELETE CASCADE,
  tipo_dado_id INTEGER REFERENCES tipos_dado(id), -- dado inicial
  base_fixa INT DEFAULT 0,
  limite_base_fixa INT,
  limite_tipo_dado_id INTEGER REFERENCES tipos_dado(id),
  imutavel BOOLEAN DEFAULT TRUE,
  UNIQUE (classe_id, atributo_id)
);
```

### 5.6 Origens e Efeitos

```sql
CREATE TYPE tipo_efeito_origem AS ENUM ('delta_atributo','conceder_item','conceder_habilidade','custom');

CREATE TABLE origens (
  id SERIAL PRIMARY KEY,
  mundo_id INTEGER NOT NULL REFERENCES mundos(id) ON DELETE CASCADE,
  slug VARCHAR(120) NOT NULL,
  nome VARCHAR(255) NOT NULL,
  descricao TEXT,
  UNIQUE (mundo_id, slug)
);

CREATE TABLE origens_efeitos (
  id SERIAL PRIMARY KEY,
  origem_id INTEGER NOT NULL REFERENCES origens(id) ON DELETE CASCADE,
  tipo tipo_efeito_origem NOT NULL,
  atributo_id INTEGER REFERENCES atributos(id),
  delta INT,
  notas JSONB -- para item/habilidade/custom
);
```

### 5.7 Habilidades

```sql
CREATE TABLE habilidades (
  id SERIAL PRIMARY KEY,
  mundo_id INTEGER NOT NULL REFERENCES mundos(id) ON DELETE CASCADE,
  slug VARCHAR(120) NOT NULL,
  nome VARCHAR(255) NOT NULL,
  descricao TEXT,
  bonus JSONB, -- buffs e debuffs
  ativa BOOLEAN DEFAULT TRUE,
  UNIQUE (mundo_id, slug)
);

-- vínculos
CREATE TABLE classes_habilidades (
  id SERIAL PRIMARY KEY,
  classe_id INTEGER NOT NULL REFERENCES classes(id) ON DELETE CASCADE,
  habilidade_id INTEGER NOT NULL REFERENCES habilidades(id) ON DELETE CASCADE,
  UNIQUE (classe_id, habilidade_id)
);

CREATE TABLE origens_habilidades (
  id SERIAL PRIMARY KEY,
  origem_id INTEGER NOT NULL REFERENCES origens(id) ON DELETE CASCADE,
  habilidade_id INTEGER NOT NULL REFERENCES habilidades(id) ON DELETE CASCADE,
  UNIQUE (origem_id, habilidade_id)
);
```

### 5.8 Itens e Armas

```sql
CREATE TYPE tipo_item AS ENUM ('arma','armadura','consumivel','acessorio','outro');

CREATE TABLE itens (
  id SERIAL PRIMARY KEY,
  mundo_id INTEGER NOT NULL REFERENCES mundos(id) ON DELETE CASCADE,
  slug VARCHAR(120) NOT NULL,
  nome VARCHAR(255) NOT NULL,
  tipo tipo_item NOT NULL,
  descricao TEXT,
  dados_dano VARCHAR(20), -- ex.: '1d6', '2d8'
  propriedades JSONB,     -- alcance, critico, etc.
  UNIQUE (mundo_id, slug)
);
```

### 5.9 NPCs

```sql
CREATE TABLE npcs (
  id SERIAL PRIMARY KEY,
  mundo_id INTEGER NOT NULL REFERENCES mundos(id) ON DELETE CASCADE,
  nome VARCHAR(255) NOT NULL,
  descricao TEXT,
  classe_id INTEGER REFERENCES classes(id),
  origem_id INTEGER REFERENCES origens(id),
  atributos JSONB, -- opcional, chave->valor
  inventario JSONB
);
```

### 5.10 Personagens e Distribuição de Pontos

```sql
CREATE TABLE personagens (
  id SERIAL PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  descricao TEXT,
  idade INT,
  mundo_id INTEGER NOT NULL REFERENCES mundos(id) ON DELETE CASCADE,
  usuario_id INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
  campanha_id INTEGER REFERENCES campanhas(id) ON DELETE SET NULL,
  classe_id INTEGER NOT NULL REFERENCES classes(id),
  origem_id INTEGER REFERENCES origens(id),
  pontos_base INT DEFAULT 0,
  niveis_dado JSONB, -- { atributo_id: nivel }
  atributos_override JSONB, -- { atributo_id: valor }
  inventario JSONB,
  criado_em TIMESTAMPTZ DEFAULT now()
);
```
