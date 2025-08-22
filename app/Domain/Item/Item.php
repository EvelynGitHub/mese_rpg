<?php

namespace App\Domain\Item;

class Item
{
    private int $id;
    private int $mundoId;
    private string $slug;
    private string $nome;
    private string $tipo;
    private ?string $descricao;
    private ?string $dadosDano;
    private ?array $propriedades;

    public function __construct(
        int $mundoId,
        string $slug,
        string $nome,
        string $tipo,
        ?string $descricao = null,
        ?string $dadosDano = null,
        ?array $propriedades = null
    ) {
        $this->mundoId = $mundoId;
        $this->slug = $slug;
        $this->nome = $nome;
        $this->tipo = $tipo;
        $this->descricao = $descricao;
        $this->dadosDano = $dadosDano;
        $this->propriedades = $propriedades;

        $this->validarTipo();
        $this->validarDadosDano();
    }

    private function validarTipo(): void
    {
        $tiposValidos = ['arma', 'armadura', 'consumivel', 'acessorio', 'outro'];
        if (!in_array($this->tipo, $tiposValidos)) {
            throw new \InvalidArgumentException('Tipo de item inválido');
        }
    }

    private function validarDadosDano(): void
    {
        if ($this->dadosDano !== null) {
            if (!preg_match('/^\d+d(4|6|8|10|12|20)$/', $this->dadosDano)) {
                throw new \InvalidArgumentException('Formato de dados de dano inválido');
            }
        }
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

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getDadosDano(): ?string
    {
        return $this->dadosDano;
    }

    public function getPropriedades(): ?array
    {
        return $this->propriedades;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
