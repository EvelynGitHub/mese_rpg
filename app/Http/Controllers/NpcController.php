<?php

namespace App\Http\Controllers;

use App\Domain\Npc\Npc;
use App\Repositories\Interfaces\NpcRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NpcController extends Controller
{
    private NpcRepositoryInterface $npcRepository;

    public function __construct(NpcRepositoryInterface $npcRepository)
    {
        $this->npcRepository = $npcRepository;
    }

    public function criar(Request $request, int $mundoId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'classe_id' => [
                'nullable',
                'integer',
                'exists:classes,id,mundo_id,' . $mundoId
            ],
            'origem_id' => [
                'nullable',
                'integer',
                'exists:origens,id,mundo_id,' . $mundoId
            ],
            'atributos' => 'nullable|array',
            'inventario' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $npc = new Npc(
            $mundoId,
            $request->input('nome'),
            $request->input('descricao'),
            $request->input('classe_id'),
            $request->input('origem_id'),
            $request->input('atributos'),
            $request->input('inventario')
        );

        $npcCriado = $this->npcRepository->criar($npc);

        return response()->json($npcCriado, 201);
    }

    public function listar(int $mundoId): JsonResponse
    {
        $npcs = $this->npcRepository->listarPorMundo($mundoId);
        return response()->json($npcs);
    }

    public function buscar(int $mundoId, int $id): JsonResponse
    {
        $npc = $this->npcRepository->buscarPorId($id, $mundoId);

        if (!$npc) {
            return response()->json(['message' => 'NPC não encontrado'], 404);
        }

        return response()->json($npc);
    }

    public function atualizar(Request $request, int $mundoId, int $id): JsonResponse
    {
        $npc = $this->npcRepository->buscarPorId($id, $mundoId);

        if (!$npc) {
            return response()->json(['message' => 'NPC não encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'classe_id' => [
                'nullable',
                'integer',
                'exists:classes,id,mundo_id,' . $mundoId
            ],
            'origem_id' => [
                'nullable',
                'integer',
                'exists:origens,id,mundo_id,' . $mundoId
            ],
            'atributos' => 'nullable|array',
            'inventario' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $npcAtualizado = new Npc(
            $mundoId,
            $request->input('nome', $npc->getNome()),
            $request->input('descricao', $npc->getDescricao()),
            $request->input('classe_id', $npc->getClasseId()),
            $request->input('origem_id', $npc->getOrigemId()),
            $request->input('atributos', $npc->getAtributos()),
            $request->input('inventario', $npc->getInventario())
        );
        $npcAtualizado->setId($id);

        if ($this->npcRepository->atualizar($npcAtualizado)) {
            return response()->json($npcAtualizado);
        }

        return response()->json(['message' => 'Erro ao atualizar NPC'], 500);
    }

    public function deletar(int $mundoId, int $id): JsonResponse
    {
        if ($this->npcRepository->deletar($id, $mundoId)) {
            return response()->json(null, 204);
        }

        return response()->json(['message' => 'NPC não encontrado'], 404);
    }
}
