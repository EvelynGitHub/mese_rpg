<?php

namespace App\UseCases;
use App\Repositories\Interfaces\LivroRepositoryInterface;

class BuscarLivrosDestaque
{
    private $repository;

    public function __construct(LivroRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): array
    {
        return $this->repository->buscarLivrosDestaque();
    }
}
