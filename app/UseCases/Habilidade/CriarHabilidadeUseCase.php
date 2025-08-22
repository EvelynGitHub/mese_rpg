<?php

namespace App\UseCases\Habilidade;

use App\Domain\Habilidade\Habilidade;
use App\Repositories\Interfaces\HabilidadeRepositoryInterface;
use InvalidArgumentException;

class CriarHabilidadeUseCase
{
    private HabilidadeRepositoryInterface $habilidadeRepository;

    public function __construct(HabilidadeRepositoryInterface $habilidadeRepository)
    {
        $this->habilidadeRepository = $habilidadeRepository;
    }

    public function executar(
        int $mundoId,
        string $slug,
        string $nome,
        ?string $descricao = null,
        ?array $bonus = null,
        bool $ativa = true
    ): Habilidade {
        // Validar slug
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            throw new InvalidArgumentException('Slug deve conter apenas letras minúsculas, números e hífens');
        }

        // Verificar se já existe habilidade com mesmo slug no mundo
        if ($this->habilidadeRepository->buscarPorSlug($slug, $mundoId)) {
            throw new InvalidArgumentException('Já existe uma habilidade com este slug neste mundo');
        }

        // Criar habilidade
        $habilidade = new Habilidade(
            $mundoId,
            $slug,
            $nome,
            $descricao,
            $bonus,
            $ativa
        );

        return $this->habilidadeRepository->criar($habilidade);
    }
}
