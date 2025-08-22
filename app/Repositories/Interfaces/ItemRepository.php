<?php

namespace App\Repositories\Interfaces;

use App\Domain\Item\Item;

interface ItemRepository
{
    public function create(Item $item): Item;
    public function update(Item $item): Item;
    public function delete(int $id): void;
    public function findById(int $id): ?Item;
    public function findBySlug(string $slug): ?Item;
    public function findAll(): array;
    public function findByMundoId(int $mundoId): array;
    public function findByTipo(string $tipo): array;
}
