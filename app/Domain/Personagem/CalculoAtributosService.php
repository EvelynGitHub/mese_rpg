<?php

declare(strict_types=1);

namespace App\Domain\Personagem;
use Illuminate\Support\Facades\DB;

class CalculoAtributosService
{
    public function calcular(Personagem $personagem): array
    {
        // 1) Carregar regras da classe
        $classeAtributos = DB::table('classes_atributos')
            ->where('classe_id', $personagem->getClasseId())
            ->get()
            ->keyBy('atributo_id');

        // 2) Carregar efeitos de origem (se houver)
        $origemEfeitos = [];
        if ($personagem->getOrigemId()) {
            $origemEfeitos = DB::table('origens_efeitos')
                ->where('origem_id', $personagem->getOrigemId())
                ->get()
                ->groupBy('atributo_id');
        }

        // 3) Resultado final por atributo
        $valores = [];

        foreach ($classeAtributos as $atributoId => $cfg) {
            $baseFixa = $cfg->base_fixa ?? 0;
            $pontos = $personagem->getPontosBaseMap()[$atributoId] ?? 0;
            $nivel = $personagem->getNiveisDado()[$atributoId] ?? 0;

            // Aplicar limite base fixa (se existir)
            if ($cfg->limite_base_fixa !== null) {
                $pontos = min($pontos, $cfg->limite_base_fixa - $baseFixa);
            }

            // Evoluir o dado do atributo (ex.: d4 → d6 → d8)
            $tipoDado = $cfg->tipo_dado_id;
            if ($nivel > 0) {
                $tipoDado = $this->evoluirDado($tipoDado, $nivel, $cfg->limite_tipo_dado_id);
            }

            // Somar efeitos da origem
            $deltaOrigem = 0;
            if (isset($origemEfeitos[$atributoId])) {
                foreach ($origemEfeitos[$atributoId] as $efeito) {
                    $deltaOrigem += $efeito->delta ?? 0;
                }
            }

            // Somar habilidades (de classe e origem)
            $bonusHabilidades = $this->bonusPorHabilidade($personagem->getId(), $atributoId);

            // Valor preliminar
            $valor = $baseFixa + $pontos + $deltaOrigem + $bonusHabilidades;

            // Override do mestre
            if (isset($personagem->getAtributosOverride()[$atributoId])) {
                $valor = $personagem->getAtributosOverride()[$atributoId];
            }

            $valores[$atributoId] = [
                'valor' => $valor,
                'tipo_dado' => $tipoDado
            ];
        }

        return $valores;
    }

    private function evoluirDado($tipoDadoId, int $nivel, ?int $limiteId): int
    {
        // Exemplo: sequência [d4, d6, d8, d10, d12, d20]
        $sequencia = DB::table('tipos_dado')->orderBy('ordem')->pluck('id')->toArray();
        $pos = array_search($tipoDadoId, $sequencia);

        if ($pos === false) {
            $pos = 0; // fallback
        }

        $novoPos = min($pos + $nivel, count($sequencia) - 1);

        $novoId = $sequencia[$novoPos];

        // Respeitar limite
        if ($limiteId && ($novoPos > array_search($limiteId, $sequencia))) {
            return $limiteId;
        }

        return $novoId;
    }

    private function bonusPorHabilidade(int $personagemId, int $atributoId): int
    {
        return DB::table('personagens_habilidades as ph')
            ->join('habilidades as h', 'h.id', '=', 'ph.habilidade_id')
            ->where('ph.personagem_id', $personagemId)
            ->get()
            ->sum(function ($hab) use ($atributoId) {
                return $hab->bonus[$atributoId] ?? 0;
            });
    }
}
