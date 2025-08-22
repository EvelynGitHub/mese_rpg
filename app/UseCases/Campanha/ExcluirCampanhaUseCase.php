<?php

namespace App\UseCases\Campanha;

use App\Repositories\Interfaces\CampanhaRepositoryInterface;

class ExcluirCampanhaUseCase
{
    private CampanhaRepositoryInterface $campanhaRepository;

    public function __construct(CampanhaRepositoryInterface $campanhaRepository)
    {
        $this->campanhaRepository = $campanhaRepository;
    }

    public function executar(int $id, int $mundoId): void
    {
        $campanha = $this->campanhaRepository->buscarPorId($id);

        if (!$campanha || $campanha->getMundoId() !== $mundoId) {
            throw new \InvalidArgumentException('Campanha não encontrada');
        }

        $this->campanhaRepository->excluir($id);
    }
}
