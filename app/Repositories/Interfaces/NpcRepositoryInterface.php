<?php

namespace App\Repositories\Interfaces;

use App\Domain\Npc\Npc;

interface NpcRepositoryInterface
{
    public function criar(Npc $npc): Npc;

    public function buscarPorId(int $id, int $mundoId): ?Npc;

    public function listarPorMundo(int $mundoId): array;

    public function atualizar(Npc $npc): bool;

    public function deletar(int $id, int $mundoId): bool;
}
