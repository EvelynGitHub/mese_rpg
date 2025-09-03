<?php

namespace App\UseCases\Classe;

use App\Domain\Classe\ClasseAtributo;
use App\Repositories\Interfaces\AtributoRepositoryInterface;
use App\Repositories\Interfaces\ClasseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AdicionarAtributoClasseUseCase
{
    private ClasseRepositoryInterface $classeRepository;
    private AtributoRepositoryInterface $atributoRepository;

    public function __construct(
        ClasseRepositoryInterface $classeRepository,
        AtributoRepositoryInterface $atributoRepository
    ) {
        $this->classeRepository = $classeRepository;
        $this->atributoRepository = $atributoRepository;
    }

    /**
     * Summary of executar
     * @param int $mundoId
     * @param int $classeId
     * @param \App\Domain\Atributo\Atributo[] $atributos
     * @throws \InvalidArgumentException
     * @return \App\Domain\Classe\ClasseAtributo[]
     */
    public function executar(int $mundoId, int $classeId, array $atributos, int $usuarioId): array
    {

        if (empty($atributos)) {
            throw new \InvalidArgumentException('Nenhum atributo fornecido para vincular à classe');
        }

        $classe = $this->classeRepository->buscarPorId($classeId, $mundoId);
        if (!$classe) {
            throw new \InvalidArgumentException('Classe não encontrada');
        }
        // Coleta todos os IDs do payload
        $atributoIds = array_column($atributos, 'atributo_id');

        // Busca todos de uma vez
        $atributosDb = $this->atributoRepository->buscarPorIds($atributoIds, $mundoId);

        // Cria um mapa id => objeto
        $atributosMap = [];
        foreach ($atributosDb as $a) {
            $atributosMap[$a->getId()] = $a;
        }

        // Verifica se já existe o vínculo
        // $atributos = $this->classeRepository->buscarAtributos($classeId);

        // lista de vínculos já existentes
        $atributosVinculados = $this->classeRepository->buscarAtributos($classeId);

        $classeAtributos = [];

        foreach ($atributos as $atributoData) {
            $atributoId = $atributoData['atributo_id'] ?? null;
            $tipoDadoId = $atributoData['tipo_dado_id'] ?? null;
            $baseFixa = $atributoData['base_fixa'] ?? 0;
            $limiteBaseFixa = $atributoData['limite_base_fixa'] ?? null;
            $limiteTipoDadoId = $atributoData['limite_tipo_dado_id'] ?? null;
            $imutavel = $atributoData['imutavel'] ?? false;

            // valida existência em memória
            if (!isset($atributosMap[$atributoId])) {
                throw new \InvalidArgumentException("Atributo {$atributoId} não encontrado");
            }

            // valida duplicidade em vínculos
            foreach ($atributosVinculados as $atributoExistente) {
                if ($atributoExistente->getAtributoId() === $atributoId) {
                    throw new \InvalidArgumentException("Atributo {$atributoId} já vinculado a esta classe");
                }
            }

            // valida sequência se precisar
            // if (!$this->classeRepository->validarSequenciaDados($mundoId, $tipoDadoId, $limiteTipoDadoId)) {
            //     throw new \InvalidArgumentException('Tipo de dado inicial deve ser menor ou igual ao limite na sequência');
            // }

            $classeAtributos[] = new ClasseAtributo(
                $classeId,
                $atributoId,
                $tipoDadoId,
                $baseFixa ?? 0,
                $limiteBaseFixa,
                $limiteTipoDadoId,
                $imutavel
            );
        }

        return DB::transaction(function () use ($classeAtributos, $mundoId, $classeId, $usuarioId) {
            // $atributoSalvo = $this->classeRepository->adicionarAtributo($classeAtributo);
            $atributoSalvo = $this->classeRepository->vincularAtributos($classeId, $classeAtributos);

            DB::table('auditoria')->insert([
                'evento' => 'ADICIONAR_ATRIBUTO_CLASSE',
                'usuario_id' => $usuarioId,
                'mundo_id' => $mundoId,
                'payload_before' => null,
                // 'payload_after' => json_encode([
                //     'classe_id' => $classeAtributo->getClasseId(),
                //     'atributo_id' => $classeAtributo->getAtributoId(),
                //     'tipo_dado_id' => $classeAtributo->getTipoDadoId(),
                //     'base_fixa' => $classeAtributo->getBaseFixa(),
                //     'limite_base_fixa' => $classeAtributo->getLimiteBaseFixa(),
                //     'limite_tipo_dado_id' => $classeAtributo->getLimiteTipoDadoId(),
                //     'imutavel' => $classeAtributo->isImutavel()
                // ]),
                'payload_after' => json_encode($classeAtributos),
                'criado_em' => now()
            ]);

            return $atributoSalvo;
        });
    }
}
