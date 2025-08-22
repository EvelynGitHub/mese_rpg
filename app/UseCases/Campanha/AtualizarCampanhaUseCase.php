<?php

namespace App\UseCases\Campanha;

use App\Domain\Campanha\Campanha;
use App\Repositories\Interfaces\CampanhaRepositoryInterface;

class AtualizarCampanhaUseCase
{
    private CampanhaRepositoryInterface $campanhaRepository;

    public function __construct(CampanhaRepositoryInterface $campanhaRepository)
    {
        $this->campanhaRepository = $campanhaRepository;
    }

    public function executar(
        int $id,
        int $mundoId,
        string $nome,
        ?string $descricao = null,
        ?string $dataInicio = null,
        ?string $dataFim = null
    ): void {
        $campanha = $this->campanhaRepository->buscarPorId($id);

        if (!$campanha || $campanha->getMundoId() !== $mundoId) {
            throw new \InvalidArgumentException('Campanha não encontrada');
        }

        $novaCampanha = new Campanha(
            $mundoId,
            $nome,
            $campanha->getCriadoPor(),
            $descricao,
            $dataInicio ? new \DateTimeImmutable($dataInicio) : null,
            $dataFim ? new \DateTimeImmutable($dataFim) : null
        );

        $novaCampanha->setId($id);
        $this->campanhaRepository->atualizar($novaCampanha);
    }
}
