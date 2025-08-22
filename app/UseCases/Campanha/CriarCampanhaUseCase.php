<?php

namespace App\UseCases\Campanha;

use App\Domain\Campanha\Campanha;
use App\Repositories\Interfaces\CampanhaRepositoryInterface;

class CriarCampanhaUseCase
{
    private CampanhaRepositoryInterface $campanhaRepository;

    public function __construct(CampanhaRepositoryInterface $campanhaRepository)
    {
        $this->campanhaRepository = $campanhaRepository;
    }

    public function executar(
        int $mundoId,
        string $nome,
        int $criadoPor,
        ?string $descricao = null,
        ?string $dataInicio = null,
        ?string $dataFim = null
    ): Campanha {
        $campanha = new Campanha(
            $mundoId,
            $nome,
            $criadoPor,
            $descricao,
            $dataInicio ? new \DateTimeImmutable($dataInicio) : null,
            $dataFim ? new \DateTimeImmutable($dataFim) : null
        );

        return $this->campanhaRepository->criar($campanha);
    }
}
