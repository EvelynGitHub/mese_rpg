<?php

namespace App\Domain\NPC;

class NPC
{
    private int $id;
    private int $mundoId;
    private string $nome;
    private string $slug;
    private ?string $descricao;
    private ?string $personalidade;
    private ?string $objetivos;
    private ?array $atributos;
    private ?array $habilidades;
    private ?array $equipamentos;
    private ?int $nivel;
    private ?int $pontosVida;
    private ?string $alinhamento;

    public function __construct(
        int $mundoId,
        string $nome,
        string $slug,
        ?string $descricao = null,
        ?string $personalidade = null,
        ?string $objetivos = null,
        ?array $atributos = null,
        ?array $habilidades = null,
        ?array $equipamentos = null,
        ?int $nivel = null,
        ?int $pontosVida = null,
        ?string $alinhamento = null
    ) {
        $this->mundoId = $mundoId;
        $this->nome = $nome;
        $this->slug = $slug;
        $this->descricao = $descricao;
        $this->personalidade = $personalidade;
        $this->objetivos = $objetivos;
        $this->atributos = $atributos;
        $this->habilidades = $habilidades;
        $this->equipamentos = $equipamentos;
        $this->nivel = $nivel;
        $this->pontosVida = $pontosVida;
        $this->alinhamento = $alinhamento;

        $this->validarNPC();
    }

    private function validarNPC(): void
    {
        if (empty($this->nome)) {
            throw new \InvalidArgumentException('Nome do NPC é obrigatório');
        }

        if ($this->pontosVida !== null && $this->pontosVida <= 0) {
            throw new \InvalidArgumentException('Pontos de vida devem ser positivos');
        }

        if ($this->nivel !== null && $this->nivel <= 0) {
            throw new \InvalidArgumentException('Nível deve ser positivo');
        }

        if ($this->alinhamento !== null) {
            $alinhamentosValidos = ['lawful_good', 'neutral_good', 'chaotic_good', 
                                  'lawful_neutral', 'true_neutral', 'chaotic_neutral',
                                  'lawful_evil', 'neutral_evil', 'chaotic_evil'];
            if (!in_array($this->alinhamento, $alinhamentosValidos)) {
                throw new \InvalidArgumentException('Alinhamento inválido');
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

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getPersonalidade(): ?string
    {
        return $this->personalidade;
    }

    public function getObjetivos(): ?string
    {
        return $this->objetivos;
    }

    public function getAtributos(): ?array
    {
        return $this->atributos;
    }

    public function getHabilidades(): ?array
    {
        return $this->habilidades;
    }

    public function getEquipamentos(): ?array
    {
        return $this->equipamentos;
    }

    public function getNivel(): ?int
    {
        return $this->nivel;
    }

    public function getPontosVida(): ?int
    {
        return $this->pontosVida;
    }

    public function getAlinhamento(): ?string
    {
        return $this->alinhamento;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
