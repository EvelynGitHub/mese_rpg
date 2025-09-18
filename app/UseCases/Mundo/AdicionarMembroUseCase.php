<?php

namespace App\UseCases\Mundo;

use App\Repositories\Interfaces\MundoRepositoryInterface;
use App\Repositories\UserRepository;

class AdicionarMembroUseCase
{
    private MundoRepositoryInterface $mundoRepository;
    private UserRepository $userRepository;

    public function __construct(MundoRepositoryInterface $mundoRepository, UserRepository $userRepository)
    {
        $this->mundoRepository = $mundoRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(int $mundoId, mixed $userId, string $role): void
    {
        // Validar papel
        if (!in_array($role, ['admin', 'mestre', 'jogador'])) {
            throw new \InvalidArgumentException('Papel inválido');
        }

        if (is_string($userId)) {
            $user = $this->userRepository->findByEmail($userId);

            if (!$user) {
                throw new \InvalidArgumentException('Usuário não encontrado');
            }
            $userId = $user->getId();
        }


        // Verificar se já existe vínculo
        $roleAtual = $this->mundoRepository->getMemberRole($mundoId, $userId);
        if ($roleAtual) {
            throw new \InvalidArgumentException('Usuário já é membro deste mundo');
        }

        $this->mundoRepository->addMember($mundoId, $userId, $role);
    }
}
