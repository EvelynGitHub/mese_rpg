<?php

namespace App\Http\Controllers;

use App\Domain\Item\Item;
use App\Domain\Origem\Origem;
use App\Domain\Origem\OrigemEfeito;
use App\Domain\Origem\OrigemHabilidades;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ItensController extends Controller
{
    public function __construct(
        private ItemRepositoryInterface $itemRepositoryInterface,
    ) {
    }

    public function criar(Request $request, int $mundoId)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:255',
            'nome' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'dados_dano' => 'nullable|string|max:255',
            // 'propriedades' => 'nullable|object'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        $item = new Item(
            $mundoId,
            $request->slug,
            $request->nome,
            $request->tipo,
            $request->descricao,
            $request->dados_dano,
            (array) $request->propriedades,
        );

        $this->itemRepositoryInterface->criar($item);

        return response()->json($item, Response::HTTP_CREATED);
    }

    public function listar(int $mundoId)
    {
        $itens = $this->itemRepositoryInterface->listarPorMundo($mundoId);
        return response()->json($itens);
    }

    public function buscar(int $mundoId, int $id)
    {
        $itens = $this->itemRepositoryInterface->buscarPorId($id, $mundoId);

        if (!$itens) {
            return response()->json(['message' => 'Origem não encontrada'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($itens);
    }

    public function atualizar(Request $request, int $mundoId, int $id)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'string|max:255',
            'nome' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'dados_dano' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $item = $this->itemRepositoryInterface->buscarPorId($id, $mundoId);

        if (!$item) {
            return response()->json(['message' => 'Origem não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $itemAtualizado = new Item(
            $mundoId,
            $request->slug ?? $item->getSlug(),
            $request->nome ?? $item->getNome(),
            $request->tipo ?? $item->getTipo(),
            $request->descricao ?? $item->getDescricao(),
            $request->dados_dano ?? $item->getDadosDano(),
            (array) $request->propriedades ?? $item->getPropriedades(),
        );
        $itemAtualizado->setId($item->getId());

        DB::transaction(function () use ($itemAtualizado) {
            $this->itemRepositoryInterface->atualizar($itemAtualizado);
        });

        return response()->json($itemAtualizado);
    }

    public function excluir(int $mundoId, int $id)
    {
        $origem = $this->itemRepositoryInterface->buscarPorId($id, $mundoId);

        if (!$origem) {
            return response()->json(['message' => 'Item não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $this->itemRepositoryInterface->excluir($id, $mundoId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
