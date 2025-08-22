<?php

namespace App\Infrastructure\Auth;

use App\Domain\Auth\JWTService;
use App\Domain\Auth\TokenDTO;
use App\Domain\User\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class FirebaseJWTService implements JWTService
{
    private string $key;
    private string $algorithm;
    private int $ttl;

    public function __construct(string $key, string $algorithm = 'HS256', int $ttl = 3600)
    {
        $this->key = $key;
        $this->algorithm = $algorithm;
        $this->ttl = $ttl;
    }

    public function generateToken(User $user, ?int $mundoId = null): TokenDTO
    {
        $now = time();
        $payload = [
            'iss' => config('app.url'),
            'iat' => $now,
            'exp' => $now + $this->ttl,
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
            'nome' => $user->getNome()
        ];

        if ($mundoId !== null) {
            $payload['mundo_id'] = $mundoId;
        }

        $token = JWT::encode($payload, $this->key, $this->algorithm);
        $refreshToken = $this->generateRefreshToken($user->getId());

        return new TokenDTO($token, $this->ttl, $refreshToken);
    }

    public function validateToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->key, $this->algorithm));
            return (array) $decoded;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Token inválido: ' . $e->getMessage());
        }
    }

    public function refreshToken(string $refreshToken): TokenDTO
    {
        try {
            $decoded = JWT::decode($refreshToken, new Key($this->key, $this->algorithm));
            $userId = $decoded->sub;

            // Aqui você pode adicionar lógica adicional para validar o refresh token
            // como verificar se ele está em uma lista negra, etc.

            // Gera um novo token de acesso
            $now = time();
            $payload = [
                'iss' => config('app.url'),
                'iat' => $now,
                'exp' => $now + $this->ttl,
                'sub' => $userId
            ];

            $newToken = JWT::encode($payload, $this->key, $this->algorithm);
            return new TokenDTO($newToken, $this->ttl, $refreshToken);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Refresh token inválido: ' . $e->getMessage());
        }
    }

    public function revokeToken(string $token): void
    {
        // Aqui você pode implementar a lógica para adicionar o token a uma lista negra
        // Por exemplo, armazenando-o em cache ou em banco de dados com um TTL
        // Cache::put('blacklisted_token_' . $token, true, $this->ttl);
    }

    private function generateRefreshToken(int $userId): string
    {
        $now = time();
        $payload = [
            'iss' => config('app.url'),
            'iat' => $now,
            'exp' => $now + ($this->ttl * 24), // 24 horas
            'sub' => $userId,
            'type' => 'refresh'
        ];

        return JWT::encode($payload, $this->key, $this->algorithm);
    }
}
