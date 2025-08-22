<?php

namespace App\UseCases\Auth;

use App\Domain\Auth\RegisterDTO;
use App\Domain\User\User;
use App\Repositories\Interfaces\UserRepository;

class RegisterUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(RegisterDTO $dto): User
    {
        // Verifica se já existe usuário com este email
        $existingUser = $this->userRepository->findByEmail($dto->getEmail());
        if ($existingUser !== null) {
            throw new \InvalidArgumentException('Email já cadastrado');
        }

        // Cria novo usuário
        $user = new User(
            $dto->getNome(),
            $dto->getEmail(),
            $dto->getSenha()
        );

        // Persiste e retorna
        return $this->userRepository->create($user);
    }
}
