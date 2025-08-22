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

    public function executar(
        int $mundoId,
        int $classeId,
        int $atributoId,
        ?int $tipoDadoId = null,
        int $baseFixa = 0,
        ?int $limiteBaseFixa = null,
        ?int $limiteTipoDadoId = null,
        bool $imutavel = false
    ): ClasseAtributo {
        $classe = $this->classeRepository->buscarPorId($classeId, $mundoId);
        if (!$classe) {
            throw new \InvalidArgumentException('Classe não encontrada');
        }

        $atributo = $this->atributoRepository->buscarPorId($atributoId, $mundoId);
        if (!$atributo || $atributo->getMundoId() !== $mundoId) {
            throw new \InvalidArgumentException('Atributo não encontrado');
        }

        // Verifica se já existe o vínculo
        $atributos = $this->classeRepository->buscarAtributos($classeId);
        foreach ($atributos as $atributoExistente) {
            if ($atributoExistente->getAtributoId() === $atributoId) {
                throw new \InvalidArgumentException('Atributo já vinculado a esta classe');
            }
        }

        // // Valida sequência de dados se tipoDadoId e limiteTipoDadoId fornecidos
        // if (!$this->classeRepository->validarSequenciaDados($mundoId, $tipoDadoId, $limiteTipoDadoId)) {
        //     throw new \InvalidArgumentException('Tipo de dado inicial deve ser menor ou igual ao limite na sequência');
        // }

        $classeAtributo = new ClasseAtributo(
            $classeId,
            $atributoId,
            $tipoDadoId,
            $baseFixa,
            $limiteBaseFixa,
            $limiteTipoDadoId,
            $imutavel
        );


        return DB::transaction(function () use ($classeAtributo, $mundoId, $classeId) {
            // $atributoSalvo = $this->classeRepository->adicionarAtributo($classeAtributo);
            $atributoSalvo = $this->classeRepository->vincularAtributos($classeId, [$classeAtributo]);

            DB::table('auditoria')->insert([
                'evento' => 'ADICIONAR_ATRIBUTO_CLASSE',
                'usuario_id' => auth()->id(),
                'mundo_id' => $mundoId,
                'payload_before' => null,
                'payload_after' => json_encode([
                    'classe_id' => $classeAtributo->getClasseId(),
                    'atributo_id' => $classeAtributo->getAtributoId(),
                    'tipo_dado_id' => $classeAtributo->getTipoDadoId(),
                    'base_fixa' => $classeAtributo->getBaseFixa(),
                    'limite_base_fixa' => $classeAtributo->getLimiteBaseFixa(),
                    'limite_tipo_dado_id' => $classeAtributo->getLimiteTipoDadoId(),
                    'imutavel' => $classeAtributo->isImutavel()
                ]),
                'criado_em' => now()
            ]);

            return $atributoSalvo;
        });
    }
}
