<?php

namespace App\Repositories\Interfaces;

use App\Domain\Origem\Origem;

interface OrigemRepositoryInterface
{
    public function criar(Origem $origem): Origem;
    public function buscarPorId(int $id, int $mundoId): ?Origem;
    public function buscarPorSlug(string $slug, int $mundoId): ?Origem;
    public function listarPorMundo(int $mundoId): array;
    public function atualizar(Origem $origem): bool;
    public function excluir(int $id, int $mundoId): bool;
    public function possuiPersonagens(int $id): bool;
    public function vincularEfeitos(int $origemId, array $efeitos): void;
    public function atualizarEfeitos(int $origemId, array $efeitos): void;
}
