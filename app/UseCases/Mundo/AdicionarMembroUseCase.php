<?php

namespace App\UseCases\Mundo;

use App\Repositories\Interfaces\MundoRepositoryInterface;

class AdicionarMembroUseCase
{
    private MundoRepositoryInterface $mundoRepository;

    public function __construct(MundoRepositoryInterface $mundoRepository)
    {
        $this->mundoRepository = $mundoRepository;
    }

    public function execute(int $mundoId, int $userId, string $role): void
    {
        // Validar papel
        if (!in_array($role, ['admin', 'mestre', 'jogador'])) {
            throw new \InvalidArgumentException('Papel inválido');
        }

        // Verificar se já existe vínculo
        $roleAtual = $this->mundoRepository->getMemberRole($mundoId, $userId);
        if ($roleAtual) {
            throw new \InvalidArgumentException('Usuário já é membro deste mundo');
        }

        $this->mundoRepository->addMember($mundoId, $userId, $role);
    }
}
