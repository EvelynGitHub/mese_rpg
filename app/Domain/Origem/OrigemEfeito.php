<?php

namespace App\Domain\Origem;

class OrigemEfeito
{
    private int $id;
    private string $tipo;
    private ?int $atributoId;
    private ?int $delta;
    private ?array $notas;

    public function __construct(
        string $tipo,
        ?int $atributoId = null,
        ?int $delta = null,
        ?array $notas = null
    ) {
        $this->tipo = $tipo;
        $this->atributoId = $atributoId;
        $this->delta = $delta;
        $this->notas = $notas;

        $this->validarTipo();
    }

    private function validarTipo(): void
    {
        $tiposValidos = ['delta_atributo', 'conceder_item', 'conceder_habilidade', 'custom'];
        if (!in_array($this->tipo, $tiposValidos)) {
            throw new \InvalidArgumentException('Tipo de efeito inválido');
        }

        if ($this->tipo === 'delta_atributo' && ($this->atributoId === null || $this->delta === null)) {
            throw new \InvalidArgumentException('Atributo e delta são obrigatórios para efeito de delta_atributo');
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getAtributoId(): ?int
    {
        return $this->atributoId;
    }

    public function getDelta(): ?int
    {
        return $this->delta;
    }

    public function getNotas(): ?array
    {
        return $this->notas;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
