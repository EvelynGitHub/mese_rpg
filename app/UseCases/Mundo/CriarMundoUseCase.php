<?php

namespace App\UseCases\Mundo;

use App\Domain\Mundo\Mundo;
use App\Repositories\Interfaces\MundoRepositoryInterface;

class CriarMundoUseCase
{
    private MundoRepositoryInterface $mundoRepository;

    public function __construct(MundoRepositoryInterface $mundoRepository)
    {
        $this->mundoRepository = $mundoRepository;
    }

    public function executar(string $nome, ?string $descricao, int $usuarioId): Mundo
    {
        // Aqui poderíamos ter validações de negócio específicas
        $mundo = new Mundo($nome, $descricao, $usuarioId);

        return $this->mundoRepository->create($mundo);
    }
}
