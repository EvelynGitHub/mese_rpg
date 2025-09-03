<?php

declare(strict_types=1);

namespace App\UseCases\Atributo;

use App\Repositories\Interfaces\AtributoRepositoryInterface;

class ListarTipoDados
{
    private AtributoRepositoryInterface $atributoRepository;

    public function __construct(AtributoRepositoryInterface $atributoRepository)
    {
        $this->atributoRepository = $atributoRepository;
    }

    public function executar(): array
    {
        return $this->atributoRepository->obterTiposDados();
    }
}
