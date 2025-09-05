<?php

namespace App\Domain\Origem;

use JsonSerializable;

class Origem implements JsonSerializable
{
    private int $id;
    private int $mundoId;
    private string $slug;
    private string $nome;
    private ?string $descricao;
    private array $efeitos;
    private array $habilidades;

    public function __construct(
        int $mundoId,
        string $slug,
        string $nome,
        ?string $descricao = null
    ) {
        $this->mundoId = $mundoId;
        $this->slug = $slug;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->efeitos = [];
        $this->habilidades = [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMundoId(): int
    {
        return $this->mundoId;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getEfeitos(): array
    {
        return $this->efeitos;
    }

    public function getHabilidades(): array
    {
        return $this->habilidades;
    }

    public function adicionarEfeito(OrigemEfeito $efeito): void
    {
        $this->efeitos[] = $efeito;
    }

    public function adicionarHabilidade(OrigemHabilidades $habilidade): void
    {
        $this->habilidades[] = $habilidade;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setEfeitos(array $efeitos): void
    {
        $this->efeitos = $efeitos;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

}
