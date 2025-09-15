<?php

namespace App\Domain\Classe;

use App\Domain\Atributo\Atributo;
use JsonSerializable;

class ClasseAtributo implements JsonSerializable
{
    private int $id;
    private int $classeId;
    private int $atributoId;
    private ?int $tipoDadoId;
    private int $baseFixa;
    private ?int $limiteBaseFixa;
    private ?int $limiteTipoDadoId;
    private bool $imutavel;
    private Atributo $atributo;

    public function __construct(
        int $classeId,
        int $atributoId,
        ?int $tipoDadoId,
        int $baseFixa = 0,
        ?int $limiteBaseFixa = null,
        ?int $limiteTipoDadoId = null,
        bool $imutavel = true
    ) {
        $this->classeId = $classeId;
        $this->atributoId = $atributoId;
        $this->tipoDadoId = $tipoDadoId;
        $this->baseFixa = $baseFixa;
        $this->limiteBaseFixa = $limiteBaseFixa;
        $this->limiteTipoDadoId = $limiteTipoDadoId;
        $this->imutavel = $imutavel;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getClasseId(): int
    {
        return $this->classeId;
    }

    public function getAtributoId(): int
    {
        return $this->atributoId;
    }

    public function getTipoDadoId(): ?int
    {
        return $this->tipoDadoId;
    }

    public function getBaseFixa(): int
    {
        return $this->baseFixa;
    }

    public function getLimiteBaseFixa(): ?int
    {
        return $this->limiteBaseFixa;
    }

    public function getLimiteTipoDadoId(): ?int
    {
        return $this->limiteTipoDadoId;
    }

    public function isImutavel(): bool
    {
        return $this->imutavel;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setAtributo(Atributo $atributo): void
    {
        $this->atributo = $atributo;
    }

    public function jsonSerialize(): array
    {
        // return get_object_vars($this); // pega atributos privados
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
