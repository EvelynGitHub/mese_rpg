<?php

namespace App\Domain\Mundo;

class Mundo
{
    private int $id;
    private string $nome;
    private ?string $descricao;
    private int $criadoPor;
    private \DateTimeInterface $criadoEm;

    public function __construct(
        string $nome,
        ?string $descricao,
        int $criadoPor
    ) {
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->criadoPor = $criadoPor;
        $this->criadoEm = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getCriadoPor(): int
    {
        return $this->criadoPor;
    }

    public function getCriadoEm(): \DateTimeInterface
    {
        return $this->criadoEm;
    }
}
