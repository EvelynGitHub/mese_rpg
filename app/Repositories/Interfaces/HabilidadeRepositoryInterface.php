<?php

namespace App\Repositories\Interfaces;

use App\Domain\Habilidade\Habilidade;

interface HabilidadeRepositoryInterface
{
    public function criar(Habilidade $habilidade): Habilidade;
    public function buscarPorId(int $id, int $mundoId): ?Habilidade;
    public function buscarPorSlug(string $slug, int $mundoId): ?Habilidade;
    public function listarPorMundo(int $mundoId): array;
    public function atualizar(Habilidade $habilidade): bool;
    public function excluir(int $id, int $mundoId): bool;
    public function vincularClasse(int $habilidadeId, int $classeId): bool;
    public function vincularOrigem(int $habilidadeId, int $origemId): bool;
    public function desvincularClasse(int $habilidadeId, int $classeId): bool;
    public function desvincularOrigem(int $habilidadeId, int $origemId): bool;
    public function listarPorClasse(int $classeId): array;
    public function listarPorOrigem(int $origemId): array;
}
