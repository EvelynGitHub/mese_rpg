<?php

namespace App\Domain\Npc;

use JsonSerializable;

class Npc implements JsonSerializable
{
    private int $id;
    private int $mundoId;
    private string $nome;
    private ?string $descricao;
    private ?string $classe;
    private ?string $origem;
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

    public function getClasse(): ?string
    {
        return $this->classe;
    }

    public function getOrigem(): ?string
    {
        return $this->origem;
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

    public function setClasse(?string $classe): void
    {
        $this->classe = $classe;
    }

    public function setOrigem(?string $origem): void
    {
        $this->origem = $origem;
    }

    public function jsonSerialize(): array
    {
        $array = get_object_vars($this); // pega atributos privados

        $snakeArray = [];
        foreach ($array as $key => $value) {
            // Converte camelCase para snake_case
            $snakeKey = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $key));
            $snakeArray[$snakeKey] = $value;
        }

        return $snakeArray;
    }
}
