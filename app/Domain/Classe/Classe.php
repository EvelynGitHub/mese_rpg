<?php

namespace App\Domain\Classe;

class Classe
{
    private int $id;
    private int $mundoId;
    private string $slug;
    private string $nome;
    private ?string $descricao;
    private array $atributos;

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
        $this->atributos = [];
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

    public function getAtributos(): array
    {
        return $this->atributos;
    }

    public function adicionarAtributo(ClasseAtributo $atributo): void
    {
        $this->atributos[] = $atributo;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setAtributos(array $atributos): void
    {
        $this->atributos = $atributos;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

}
