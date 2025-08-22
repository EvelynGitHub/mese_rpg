<?php

namespace App\Domain\Campanha;

use DateTimeInterface;

class Campanha
{
    private int $id;
    private int $mundoId;
    private string $nome;
    private ?string $descricao;
    private ?DateTimeInterface $dataInicio;
    private ?DateTimeInterface $dataFim;
    private int $criadoPor;
    private DateTimeInterface $criadoEm;
    private array $sessoes;

    public function __construct(
        int $mundoId,
        string $nome,
        int $criadoPor,
        ?string $descricao = null,
        ?DateTimeInterface $dataInicio = null,
        ?DateTimeInterface $dataFim = null
    ) {
        $this->mundoId = $mundoId;
        $this->nome = $nome;
        $this->criadoPor = $criadoPor;
        $this->descricao = $descricao;
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->criadoEm = new \DateTimeImmutable();
        $this->sessoes = [];

        $this->validarDatas();
    }

    private function validarDatas(): void
    {
        if ($this->dataInicio && $this->dataFim && $this->dataFim < $this->dataInicio) {
            throw new \InvalidArgumentException('Data fim não pode ser anterior à data início');
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

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getDataInicio(): ?DateTimeInterface
    {
        return $this->dataInicio;
    }

    public function getDataFim(): ?DateTimeInterface
    {
        return $this->dataFim;
    }

    public function getCriadoPor(): int
    {
        return $this->criadoPor;
    }

    public function getCriadoEm(): DateTimeInterface
    {
        return $this->criadoEm;
    }

    public function getSessoes(): array
    {
        return $this->sessoes;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setSessoes(array $sessoes): void
    {
        $this->sessoes = $sessoes;
    }
}
