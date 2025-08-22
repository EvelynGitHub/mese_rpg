<?php

namespace App\Domain\Atributo;

class Atributo
{
    private int $id;
    private int $mundoId;
    private string $chave;
    private string $nome;
    private ?string $descricao;
    private bool $exibir;

    public function __construct(
        int $mundoId,
        string $chave,
        string $nome,
        ?string $descricao = null,
        bool $exibir = true
    ) {
        $this->mundoId = $mundoId;
        $this->chave = $chave;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->exibir = $exibir;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMundoId(): int
    {
        return $this->mundoId;
    }

    public function getChave(): string
    {
        return $this->chave;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function isExibir(): bool
    {
        return $this->exibir;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
