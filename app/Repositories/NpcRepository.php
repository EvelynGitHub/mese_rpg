<?php

namespace App\Repositories;

use App\Domain\Npc\Npc;
use App\Repositories\Interfaces\NpcRepositoryInterface;
use Illuminate\Support\Facades\DB;

class NpcRepository implements NpcRepositoryInterface
{
    public function criar(Npc $npc): Npc
    {
        $id = DB::table('npcs')->insertGetId([
            'mundo_id' => $npc->getMundoId(),
            'nome' => $npc->getNome(),
            'descricao' => $npc->getDescricao(),
            'classe_id' => $npc->getClasseId(),
            'origem_id' => $npc->getOrigemId(),
            'atributos' => json_encode($npc->getAtributos()),
            'inventario' => json_encode($npc->getInventario()),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $npc->setId($id);
        return $npc;
    }

    public function buscarPorId(int $id, int $mundoId): ?Npc
    {
        $npcData = DB::table('npcs')
            ->where('id', $id)
            ->where('mundo_id', $mundoId)
            ->first();

        if (!$npcData) {
            return null;
        }

        $npc = new Npc(
            $npcData->mundo_id,
            $npcData->nome,
            $npcData->descricao,
            $npcData->classe_id,
            $npcData->origem_id,
            json_decode($npcData->atributos, true),
            json_decode($npcData->inventario, true)
        );
        $npc->setId($npcData->id);

        return $npc;
    }

    public function listarPorMundo(int $mundoId): array
    {
        $npcsData = DB::table('npcs')
            ->where('mundo_id', $mundoId)
            ->get();

        return $npcsData->map(function ($npcData) {
            $npc = new Npc(
                $npcData->mundo_id,
                $npcData->nome,
                $npcData->descricao,
                $npcData->classe_id,
                $npcData->origem_id,
                json_decode($npcData->atributos, true),
                json_decode($npcData->inventario, true)
            );
            $npc->setId($npcData->id);
            return $npc;
        })->all();
    }

    public function atualizar(Npc $npc): bool
    {
        return DB::table('npcs')
            ->where('id', $npc->getId())
            ->where('mundo_id', $npc->getMundoId())
            ->update([
                'nome' => $npc->getNome(),
                'descricao' => $npc->getDescricao(),
                'classe_id' => $npc->getClasseId(),
                'origem_id' => $npc->getOrigemId(),
                'atributos' => json_encode($npc->getAtributos()),
                'inventario' => json_encode($npc->getInventario()),
                'updated_at' => now(),
            ]) > 0;
    }

    public function deletar(int $id, int $mundoId): bool
    {
        return DB::table('npcs')
            ->where('id', $id)
            ->where('mundo_id', $mundoId)
            ->delete() > 0;
    }
}
