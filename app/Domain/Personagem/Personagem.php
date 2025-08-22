<?php

namespace App\Domain\Personagem;

class Personagem
{
    private int $id;
    private string $nome;
    private ?string $descricao;
    private ?int $idade;
    private int $mundoId;
    private int $usuarioId;
    private ?int $campanhaId;
    private int $classeId;
    private ?int $origemId;
    private int $pontosBase;
    private ?array $niveisDado;
    private ?array $atributosOverride;
    private ?array $inventario;
    private \DateTime $criadoEm;
    private array $pontosBaseMap;

    public function __construct(
        string $nome,
        int $mundoId,
        int $usuarioId,
        int $classeId,
        ?string $descricao = null,
        ?int $idade = null,
        ?int $campanhaId = null,
        ?int $origemId = null,
        int $pontosBase = 0,
        ?array $pontosBaseMap = [],
        ?array $niveisDado = null,
        ?array $atributosOverride = null,
        ?array $inventario = null
    ) {
        $this->nome = $nome;
        $this->mundoId = $mundoId;
        $this->usuarioId = $usuarioId;
        $this->classeId = $classeId;
        $this->descricao = $descricao;
        $this->idade = $idade;
        $this->campanhaId = $campanhaId;
        $this->origemId = $origemId;
        $this->pontosBase = $pontosBase;
        $this->pontosBaseMap = $pontosBaseMap ?? [];
        $this->niveisDado = $niveisDado;
        $this->atributosOverride = $atributosOverride;
        $this->inventario = $inventario;
        $this->criadoEm = new \DateTime();

        $this->validarPersonagem();
    }

    private function validarPersonagem(): void
    {
        if (empty($this->nome)) {
            throw new \InvalidArgumentException('Nome do personagem é obrigatório');
        }

        if ($this->idade !== null && $this->idade < 0) {
            throw new \InvalidArgumentException('Idade deve ser um número positivo');
        }

        if ($this->pontosBase < 0) {
            throw new \InvalidArgumentException('Pontos base não podem ser negativos');
        }

        if ($this->niveisDado !== null) {
            foreach ($this->niveisDado as $atributoId => $nivel) {
                if (!is_int($nivel) || $nivel < 0) {
                    throw new \InvalidArgumentException(
                        'Nível do dado para o atributo ' . $atributoId . ' deve ser um número inteiro positivo'
                    );
                }
            }
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getIdade(): ?int
    {
        return $this->idade;
    }

    public function getMundoId(): int
    {
        return $this->mundoId;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getCampanhaId(): ?int
    {
        return $this->campanhaId;
    }

    public function getClasseId(): int
    {
        return $this->classeId;
    }

    public function getOrigemId(): ?int
    {
        return $this->origemId;
    }

    public function getPontosBase(): int
    {
        return $this->pontosBase;
    }

    public function getNiveisDado(): ?array
    {
        return $this->niveisDado;
    }

    public function getAtributosOverride(): ?array
    {
        return $this->atributosOverride;
    }

    public function getInventario(): ?array
    {
        return $this->inventario;
    }

    public function getCriadoEm(): \DateTime
    {
        return $this->criadoEm;
    }

    public function getPontosBaseMap(): array
    {
        return $this->pontosBaseMap;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setCampanhaId(?int $campanhaId): void
    {
        $this->campanhaId = $campanhaId;
    }

    public function setAtributosOverride(?array $atributosOverride): void
    {
        $this->atributosOverride = $atributosOverride;
    }

    public function setInventario(?array $inventario): void
    {
        $this->inventario = $inventario;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function setPontosBaseMap(array $pontosBaseMap): void
    {
        $this->pontosBaseMap = $pontosBaseMap;
    }

    public function setNiveisDado(?array $niveisDado): void
    {
        $this->niveisDado = $niveisDado;
    }


}
