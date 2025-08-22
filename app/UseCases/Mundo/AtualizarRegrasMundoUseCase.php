<?php

namespace App\UseCases\Mundo;

use App\Domain\Mundo\MundoRegras;
use App\Repositories\Interfaces\MundoRepositoryInterface;

class AtualizarRegrasMundoUseCase
{
    private MundoRepositoryInterface $mundoRepository;

    public function __construct(MundoRepositoryInterface $mundoRepository)
    {
        $this->mundoRepository = $mundoRepository;
    }

    public function execute(
        int $mundoId,
        int $pontosBasePorPersonagem,
        int $niveisDadoPorPersonagem,
        array $sequenciaDados,
        ?int $limiteMaxTipoDadoId,
        bool $permitePvp,
        bool $permitePve
    ): void {
        // Criar ou atualizar regras
        $regras = new MundoRegras(
            $mundoId,
            $pontosBasePorPersonagem,
            $niveisDadoPorPersonagem,
            $sequenciaDados,
            $limiteMaxTipoDadoId,
            $permitePvp,
            $permitePve
        );

        $regrasAtuais = $this->mundoRepository->getRules($mundoId);
        if ($regrasAtuais) {
            $this->mundoRepository->updateRules($regras);
        } else {
            $this->mundoRepository->createRules($regras);
        }
    }
}
