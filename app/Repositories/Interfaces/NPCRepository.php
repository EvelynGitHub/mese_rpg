<?php

namespace App\Repositories\Interfaces;

use App\Domain\NPC\NPC;
interface NPCRepository
{
    public function create(NPC $npc): NPC;
    public function update(NPC $npc): NPC;
    public function delete(int $id): void;
    public function findById(int $id): ?NPC;
    public function findBySlug(string $slug): ?NPC;
    public function findAll(): array;
    public function findByMundoId(int $mundoId): array;
    public function findByNome(string $nome): array;
    public function findByAlinhamento(string $alinhamento): array;
}
