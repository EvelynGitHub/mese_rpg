<?php

namespace App\UseCases\Personagem;

use App\Repositories\Interfaces\PersonagemRepositoryInterface;
use App\Repositories\Interfaces\ClasseRepositoryInterface;
use App\Repositories\Interfaces\OrigemRepositoryInterface;
use App\Repositories\Interfaces\HabilidadeRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CalcularAtributosPersonagemUseCase
{
    private PersonagemRepositoryInterface $personagemRepository;
    private ClasseRepositoryInterface $classeRepository;
    private OrigemRepositoryInterface $origemRepository;
    private HabilidadeRepositoryInterface $habilidadeRepository;

    public function __construct(
        PersonagemRepositoryInterface $personagemRepository,
        ClasseRepositoryInterface $classeRepository,
        OrigemRepositoryInterface $origemRepository,
        HabilidadeRepositoryInterface $habilidadeRepository
    ) {
        $this->personagemRepository = $personagemRepository;
        $this->classeRepository = $classeRepository;
        $this->origemRepository = $origemRepository;
        $this->habilidadeRepository = $habilidadeRepository;
    }

    public function executar(int $personagemId, int $mundoId, bool $preview = false): array
    {
        $personagem = $this->personagemRepository->buscarPorId($personagemId, $mundoId);
        if (!$personagem) {
            throw new InvalidArgumentException('Personagem não encontrado');
        }

        // Carregar todas as entidades necessárias
        $classe = $this->classeRepository->buscarPorId($personagem->getClasseId(), $mundoId);
        $origem = $personagem->getOrigemId()
            ? $this->origemRepository->buscarPorId($personagem->getOrigemId(), $mundoId)
            : null;

        $atributosCalculados = [];

        // Para cada atributo da classe
        foreach ($classe->getAtributos() as $classeAtributo) {
            $atributoId = $classeAtributo->getAtributoId();
            $valor = $classeAtributo->getBaseFixa();

            // 1. Adicionar pontos base do personagem
            // $pontosBaseMap = $personagem->getPontosBaseMap() ?? [];
            $pontosBaseMap = [];
            $valor += $pontosBaseMap[$atributoId] ?? 0;

            // 2. Adicionar deltas da origem
            if ($origem) {
                foreach ($origem->getEfeitos() as $efeito) {
                    if ($efeito->getTipo() === 'delta_atributo' &&
                        $efeito->getAtributoId() === $atributoId) {
                        $valor += $efeito->getDelta();
                    }
                }
            }

            // 3. Calcular bonus de habilidades
            $habilidadesClasse = $this->habilidadeRepository->listarPorClasse($classe->getId());
            $habilidadesOrigem = $origem
                ? $this->habilidadeRepository->listarPorOrigem($origem->getId())
                : [];

            $todasHabilidades = array_merge($habilidadesClasse, $habilidadesOrigem);
            foreach ($todasHabilidades as $habilidade) {
                if ($habilidade->isAtiva()) {
                    $bonus = $habilidade->getBonus();
                    if ($bonus && isset($bonus[$atributoId])) {
                        $valor += $bonus[$atributoId];
                    }
                }
            }

            // 4. Calcular dado atual (ou valor esperado se preview)
            $dadoAtual = $this->calcularDadoAtual(
                $classeAtributo,
                $personagem->getNiveisDado()[$atributoId] ?? 0,
                $mundoId
            );

            if ($preview) {
                // Valor esperado do dado = (1 + N) / 2
                $valor += ($dadoAtual->faces + 1) / 2;
            }

            // 5. Aplicar override do mestre se existir
            $overrides = $personagem->getAtributosOverride() ?? [];
            if (isset($overrides[$atributoId])) {
                $valor = $overrides[$atributoId];
            }

            $atributosCalculados[$atributoId] = [
                'valor_final' => $valor,
                'tipo_dado_atual' => $dadoAtual->id,
                'faces_dado' => $dadoAtual->faces,
                'imutavel' => $classeAtributo->isImutavel()
            ];
        }

        return $atributosCalculados;
    }

    private function calcularDadoAtual($classeAtributo, int $niveis, int $mundoId): object
    {
        // Buscar sequência de dados do mundo
        $regras = DB::table('mundos_regras')
            ->where('mundo_id', $mundoId)
            ->first();

        $sequenciaDados = json_decode($regras->sequencia_dados);
        $dadoInicial = $classeAtributo->getTipoDadoId() ?? $sequenciaDados[0];

        // Buscar dado inicial
        $tipoDadoInicial = DB::table('tipos_dado')
            ->where('id', $dadoInicial)
            ->first();

        // Encontrar índice atual na sequência
        $idxAtual = array_search($tipoDadoInicial->faces, $sequenciaDados);

        // Calcular índice final considerando limites
        $idxFinal = min(
            $idxAtual + $niveis,
            array_search(
                DB::table('tipos_dado')
                    ->where('id', $classeAtributo->getLimiteTipoDadoId() ?? $regras->limite_max_tipo_dado_id ?? PHP_INT_MAX)
                    ->value('faces'),
                $sequenciaDados
            )
        );

        // Buscar dado final
        return DB::table('tipos_dado')
            ->where('faces', $sequenciaDados[$idxFinal])
            ->first();
    }
}
