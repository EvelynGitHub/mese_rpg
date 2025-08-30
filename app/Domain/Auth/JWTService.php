<?php

namespace App\Domain\Auth;

use App\Domain\User\User;

interface JWTService
{
    /**
     * Gera um novo token JWT para o usuário
     */
    public function generateToken(User $user, array $mundoId = []): TokenDTO;

    /**
     * Valida um token JWT e retorna os dados do payload
     */
    public function validateToken(string $token): array;

    /**
     * Atualiza um token JWT usando o refresh token
     */
    public function refreshToken(string $refreshToken): TokenDTO;

    /**
     * Revoga um token JWT
     */
    public function revokeToken(string $token): void;
}
