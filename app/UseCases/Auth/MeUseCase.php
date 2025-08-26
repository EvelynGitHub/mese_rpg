<?php

namespace App\UseCases\Auth;

use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;


class MeUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(int $userId): array
    {
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new \InvalidArgumentException('Usuário não encontrado');
        }

        // Busca mundos e papéis do usuário
        $mundos = $this->userRepository->getUsuariosMundo($user->getId());

        return [
            'user' => [
                'id' => $user->getId(),
                'nome' => $user->getNome(),
                'email' => $user->getEmail()
            ],
            'mundos' => $mundos
        ];
    }
}
