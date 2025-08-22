<?php

namespace App\Domain\Mundo;

class MundoRegras
{
    private int $id;
    private int $mundoId;
    private int $pontosBasePorPersonagem;
    private int $niveisDadoPorPersonagem;
    private array $sequenciaDados;
    private ?int $limiteMaxTipoDadoId;
    private bool $permitePvp;
    private bool $permitePve;

    public function __construct(
        int $mundoId,
        int $pontosBasePorPersonagem = 0,
        int $niveisDadoPorPersonagem = 0,
        array $sequenciaDados = [4, 6, 8, 10, 12, 20],
        ?int $limiteMaxTipoDadoId = null,
        bool $permitePvp = false,
        bool $permitePve = true
    ) {
        $this->mundoId = $mundoId;
        $this->pontosBasePorPersonagem = $pontosBasePorPersonagem;
        $this->niveisDadoPorPersonagem = $niveisDadoPorPersonagem;
        $this->sequenciaDados = $sequenciaDados;
        $this->limiteMaxTipoDadoId = $limiteMaxTipoDadoId;
        $this->permitePvp = $permitePvp;
        $this->permitePve = $permitePve;

        $this->validar();
    }

    private function validar(): void
    {
        if ($this->pontosBasePorPersonagem < 0) {
            throw new \InvalidArgumentException('Pontos base por personagem não pode ser negativo');
        }

        if ($this->niveisDadoPorPersonagem < 0) {
            throw new \InvalidArgumentException('Níveis de dado por personagem não pode ser negativo');
        }

        if (empty($this->sequenciaDados)) {
            throw new \InvalidArgumentException('Sequência de dados não pode estar vazia');
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

    public function getPontosBasePorPersonagem(): int
    {
        return $this->pontosBasePorPersonagem;
    }

    public function getNiveisDadoPorPersonagem(): int
    {
        return $this->niveisDadoPorPersonagem;
    }

    public function getSequenciaDados(): array
    {
        return $this->sequenciaDados;
    }

    public function getLimiteMaxTipoDadoId(): ?int
    {
        return $this->limiteMaxTipoDadoId;
    }

    public function getPermitePvp(): bool
    {
        return $this->permitePvp;
    }

    public function getPermitePve(): bool
    {
        return $this->permitePve;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
