<?php

namespace App\Domain\Classe;

use App\Domain\Habilidade\Habilidade;
use JsonSerializable;

class ClasseHabilidades implements JsonSerializable
{
    private int $id;
    private int $classeId;
    private int $habilidadeId;
    private ?Habilidade $habilidade;

    public function __construct(
        int $classeId,
        int $habilidadeId,
        ?Habilidade $habilidade
    ) {
        $this->classeId = $classeId;
        $this->habilidadeId = $habilidadeId;
        $this->habilidade = $habilidade;
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

    public function getHabilidadeId(): int
    {
        return $this->habilidadeId;
    }

    public function getHabilidade(): Habilidade
    {
        return $this->habilidade;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this); // pega atributos privados
    }
}
