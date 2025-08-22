<?php

namespace App\UseCases\Atributo;

use App\Domain\Atributo\Atributo;
use App\Repositories\Interfaces\AtributoRepositoryInterface;
use InvalidArgumentException;

class CriarAtributoUseCase
{
    private AtributoRepositoryInterface $atributoRepository;

    public function __construct(AtributoRepositoryInterface $atributoRepository)
    {
        $this->atributoRepository = $atributoRepository;
    }

    public function executar(
        int $mundoId,
        string $chave,
        string $nome,
        ?string $descricao = null,
        bool $exibir = true
    ): Atributo {
        // Validar chave (slug-like)
        if (!preg_match('/^[a-z0-9_]+$/', $chave)) {
            throw new InvalidArgumentException('Chave deve conter apenas letras minúsculas, números e underscores');
        }

        // Verificar se já existe atributo com mesma chave no mundo
        if ($this->atributoRepository->buscarPorChave($chave, $mundoId)) {
            throw new InvalidArgumentException('Já existe um atributo com esta chave neste mundo');
        }

        $atributo = new Atributo(
            $mundoId,
            $chave,
            $nome,
            $descricao,
            $exibir
        );

        return $this->atributoRepository->criar($atributo);
    }
}
