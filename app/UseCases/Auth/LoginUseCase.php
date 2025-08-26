<?php

namespace App\UseCases\Auth;

use App\Domain\Auth\JWTService;
use App\Domain\Auth\LoginDTO;
use App\Domain\Auth\TokenDTO;
use App\Domain\User\Exception\UserNotFoundException;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;

class LoginUseCase
{
    private UserRepository $userRepository;
    private JWTService $jwtService;

    public function __construct(
        UserRepository $userRepository,
        JWTService $jwtService
    ) {
        $this->userRepository = $userRepository;
        $this->jwtService = $jwtService;
    }

    public function execute(LoginDTO $dto): array
    {
        // Busca usuário pelo email
        $user = $this->userRepository->findByEmail($dto->getEmail());
        if ($user === null) {
            throw new UserNotFoundException('Usuário não encontrado');
        }

        // Verifica senha
        if (!$user->verificarSenha($dto->getSenha())) {
            throw new \InvalidArgumentException('Email ou senha inválidos');
        }

        // Gera token JWT
        $token = $this->jwtService->generateToken($user);

        // Retorna token e dados do usuário
        return [
            'token' => $token->toArray(),
            'user' => [
                'id' => $user->getId(),
                'nome' => $user->getNome(),
                'email' => $user->getEmail()
            ]
        ];
    }
}
