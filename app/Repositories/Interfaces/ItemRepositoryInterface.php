<?php

namespace App\Repositories\Interfaces;

use App\Domain\Item\Item;

interface ItemRepositoryInterface
{
    public function criar(Item $item): Item;
    public function atualizar(Item $item): bool;
    public function excluir(int $id, int $mundoId): bool;
    public function buscarPorId(int $id, int $mundoId): ?Item;
    public function listarPorMundo(int $mundoId): array;
    // public function buscarPorSlug(string $slug, int $mundoId): ?Item;
    // public function findAll(): array;
    // public function findByTipo(string $tipo): array;
}
