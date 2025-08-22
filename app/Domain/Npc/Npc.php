<?php

namespace App\Domain\Npc;

class Npc
{
    private int $id;
    private int $mundoId;
    private string $nome;
    private ?string $descricao;
    private ?int $classeId;
    private ?int $origemId;
    private ?array $atributos;
    private ?array $inventario;

    public function __construct(
        int $mundoId,
        string $nome,
        ?string $descricao = null,
        ?int $classeId = null,
        ?int $origemId = null,
        ?array $atributos = null,
        ?array $inventario = null
    ) {
        $this->mundoId = $mundoId;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->classeId = $classeId;
        $this->origemId = $origemId;
        $this->atributos = $atributos;
        $this->inventario = $inventario;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMundoId(): int
    {
        return $this->mundoId;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getClasseId(): ?int
    {
        return $this->classeId;
    }

    public function getOrigemId(): ?int
    {
        return $this->origemId;
    }

    public function getAtributos(): ?array
    {
        return $this->atributos;
    }

    public function getInventario(): ?array
    {
        return $this->inventario;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
