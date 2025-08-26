<?php

namespace App\Domain\User;

class User
{
    private int $id;
    private string $nome;
    private string $email;
    private string $senhaHash;
    private \DateTime $criadoEm;
    private \DateTime $atualizadoEm;

    public function __construct(
        string $nome,
        string $email,
        string $senha
    ) {
        $this->nome = $nome;
        $this->email = $email;
        $this->senhaHash = password_hash($senha, PASSWORD_BCRYPT);
        $this->criadoEm = new \DateTime();
        $this->atualizadoEm = new \DateTime();

        $this->validarUsuario();
    }

    private function validarUsuario(): void
    {
        if (empty($this->nome)) {
            throw new \InvalidArgumentException('Nome do usuário é obrigatório');
        }

        if (empty($this->email)) {
            throw new \InvalidArgumentException('Email é obrigatório');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email inválido');
        }

        if (strlen($this->senhaHash) < 60) { // Tamanho mínimo do hash bcrypt
            throw new \InvalidArgumentException('Senha inválida');
        }
    }

    public function verificarSenha(string $senha): bool
    {
        return password_verify($senha, $this->senhaHash);
    }

    public function atualizarSenha(string $senha): void
    {
        $this->senhaHash = password_hash($senha, PASSWORD_BCRYPT);
        $this->atualizadoEm = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSenha(): string
    {
        return $this->senhaHash;
    }

    public function getCriadoEm(): \DateTime
    {
        return $this->criadoEm;
    }

    public function getAtualizadoEm(): \DateTime
    {
        return $this->atualizadoEm;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
        $this->atualizadoEm = new \DateTime();
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email inválido');
        }
        $this->email = $email;
        $this->atualizadoEm = new \DateTime();
    }

    public function setSenhaHash(string $senhaHash): void
    {
        $this->senhaHash = $senhaHash;
    }
}
