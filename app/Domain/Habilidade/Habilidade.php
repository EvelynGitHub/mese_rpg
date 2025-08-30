<?php

namespace App\Domain\Habilidade;

class Habilidade
{
    private int $id;
    private int $mundoId;
    private string $slug;
    private string $nome;
    private ?string $descricao;
    private ?array $bonus;
    private bool $ativa;

    public function __construct(
        int $mundoId,
        string $slug,
        string $nome,
        ?string $descricao = null,
        ?array $bonus = null,
        bool $ativa = true
    ) {
        $this->mundoId = $mundoId;
        $this->slug = $slug;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->bonus = $bonus;
        $this->ativa = $ativa;

        $this->validarBonus();
    }

    private function validarBonus(): void
    {
        if ($this->bonus !== null) {
            foreach ($this->bonus as $chave => $valor) {
                if (!is_string($chave) || !is_numeric($valor)) {
                    throw new \InvalidArgumentException(
                        'Bonus deve ser um array associativo com chaves string e valores numéricos'
                    );
                }
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

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getBonus(): ?array
    {
        return $this->bonus;
    }

    public function isAtiva(): bool
    {
        return $this->ativa;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    // toArray para retornar os Dados

}
