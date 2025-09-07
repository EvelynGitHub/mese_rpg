<?php

namespace App\Repositories;

use App\Domain\Item\Item;
use App\Domain\Item\ItemEfeito;
use App\Domain\Item\ItemHabilidades;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ItensRepository implements ItemRepositoryInterface
{
    public function criar(Item $item): Item
    {
        $id = DB::table('itens')->insertGetId([
            'mundo_id' => $item->getMundoId(),
            'slug' => $item->getSlug(),
            'nome' => $item->getNome(),
            'tipo' => $item->getTipo(),
            'descricao' => $item->getDescricao(),
            'dados_dano' => $item->getDadosDano(),
            'propriedades' => json_encode($item->getPropriedades() ?? [])
            // 'criado_em' => now()
        ]);

        $item->setId($id);

        return $item;
    }


    public function buscarPorId(int $id, int $mundoId): ?Item
    {
        $item = DB::table('itens')
            ->where('id', $id)
            ->where('mundo_id', $mundoId)
            ->first();

        if (!$item) {
            return null;
        }

        $novaItem = new Item(
            $item->mundo_id,
            $item->slug,
            $item->nome,
            $item->tipo,
            $item->descricao,
            $item->dados_dano,
            json_decode($item->propriedades, true)
        );
        $novaItem->setId($item->id);

        return $novaItem;
    }


    public function listarPorMundo(int $mundoId): array
    {
        $itens = DB::table('itens')
            ->where('mundo_id', $mundoId)
            ->get();

        return $itens->map(function ($item) {
            $novaItem = new Item(
                $item->mundo_id,
                $item->slug,
                $item->nome,
                $item->tipo,
                $item->descricao,
                $item->dados_dano,
                json_decode($item->propriedades, true)
            );
            $novaItem->setId($item->id);
            return $novaItem;
        })->all();
    }

    public function atualizar(Item $item): bool
    {
        $resultado = DB::table('itens')
            ->where('id', $item->getId())
            ->where('mundo_id', $item->getMundoId())
            ->update([
                'slug' => $item->getSlug(),
                'nome' => $item->getNome(),
                'descricao' => $item->getDescricao(),
                'dados_dano' => $item->getDadosDano(),
                'propriedades' => $item->getPropriedades()
            ]);

        return $resultado;
    }

    public function excluir(int $id, int $mundoId): bool
    {
        return DB::table('itens')
            ->where('id', $id)
            ->where('mundo_id', $mundoId)
            ->delete() > 0;
    }
}
