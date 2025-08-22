<?php

namespace App\Domain\Auth;

class TokenDTO
{
    private string $accessToken;
    private ?string $refreshToken;
    private int $expiresIn;

    public function __construct(
        string $accessToken,
        int $expiresIn,
        ?string $refreshToken = null
    ) {
        $this->accessToken = $accessToken;
        $this->expiresIn = $expiresIn;
        $this->refreshToken = $refreshToken;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'expires_in' => $this->expiresIn,
            'refresh_token' => $this->refreshToken,
        ];
    }
}
