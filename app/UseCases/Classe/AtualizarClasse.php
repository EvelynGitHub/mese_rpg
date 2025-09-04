<?php

declare(strict_types=1);

namespace App\UseCases\Classe;

use App\Domain\Classe\ClasseAtributo;
use App\Domain\Classe\ClasseHabilidades;
use App\Repositories\Interfaces\ClasseRepositoryInterface;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

class AtualizarClasse
{
    public function __construct(
        private ClasseRepositoryInterface $classeRepository
    ) {
    }

    public function executar(int $mundoId, int $classeId, array $dados): void
    {
        $classe = $this->classeRepository->buscarPorId($classeId, $mundoId);

        if (!$classe) {
            throw new InvalidArgumentException("A classe informada não existe!");
        }

        if (!empty($dados['nome'])) {
            $classe->setNome($dados['nome']);
        }
        if (!empty($dados['descricao'])) {
            $classe->setDescricao($dados['descricao'] ?? null);
        }

        DB::transaction(function () use ($classe, $dados) {
            $this->classeRepository->atualizar($classe);

            if (isset($dados['atributos'])) {
                $atributos = array_map(function ($attr) {
                    return new ClasseAtributo(
                        0,
                        (int) $attr['atributo_id'],
                        (int) ($attr['tipo_dado_id'] ?? 0) ?: null,
                        (int) $attr['base_fixa'] ?? 0,
                        (int) ($attr['limite_base_fixa'] ?? 0) ?: null,
                        (int) ($attr['limite_tipo_dado_id'] ?? 0) ?: null,
                        $attr['imutavel'] ?? false
                    );
                }, $dados['atributos']);

                $this->classeRepository->atualizarAtributos($classe->getId(), $atributos);
            }

            if (isset($dados['habilidades'])) {
                $habilidades = array_map(function ($attr) {
                    return new ClasseHabilidades(
                        0,
                        (int) $attr['habilidade_id'],
                        null
                    );
                }, $dados['habilidades']);

                $this->classeRepository->atualizarHabilidades($classe->getId(), $habilidades);
            }
        });
    }
}
