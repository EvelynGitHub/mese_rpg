<?php

namespace App\Domain\Campanha;

use DateTimeInterface;

class Sessao
{
    private int $id;
    private int $campanhaId;
    private DateTimeInterface $dataHora;
    private ?string $resumo;

    public function __construct(
        int $campanhaId,
        DateTimeInterface $dataHora,
        ?string $resumo = null
    ) {
        $this->campanhaId = $campanhaId;
        $this->dataHora = $dataHora;
        $this->resumo = $resumo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCampanhaId(): int
    {
        return $this->campanhaId;
    }

    public function getDataHora(): DateTimeInterface
    {
        return $this->dataHora;
    }

    public function getResumo(): ?string
    {
        return $this->resumo;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
