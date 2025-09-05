<?php

namespace App\Http\Controllers;

use App\Domain\Origem\Origem;
use App\Domain\Origem\OrigemEfeito;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\OrigemRepositoryInterface;
use App\UseCases\Origem\CriarOrigemUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class OrigemController extends Controller
{
    public function __construct(
        private OrigemRepositoryInterface $origemRepository,
        private CriarOrigemUseCase $criarOrigemUseCase
    ) {
    }

    public function criar(Request $request, int $mundoId)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:255',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'habilidades' => 'nullable|array',
            'habilidades.*.habilidade_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $origem = $this->criarOrigemUseCase->executar(
            $mundoId,
            $request->slug,
            $request->nome,
            $request->descricao,
            $request->efeitos,
            $request->habilidades,
        );

        return response()->json($origem, Response::HTTP_CREATED);
    }

    public function listar(int $mundoId)
    {
        $origens = $this->origemRepository->listarPorMundo($mundoId);
        return response()->json($origens);
    }

    public function buscar(int $mundoId, int $id)
    {
        $origem = $this->origemRepository->buscarPorId($id, $mundoId);

        if (!$origem) {
            return response()->json(['message' => 'Origem não encontrada'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($origem);
    }

    public function atualizar(Request $request, int $mundoId, int $id)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'string|max:255',
            'nome' => 'string|max:255',
            'descricao' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $origem = $this->origemRepository->buscarPorId($id, $mundoId);

        if (!$origem) {
            return response()->json(['message' => 'Origem não encontrada'], Response::HTTP_NOT_FOUND);
        }

        if ($request->has('slug') && $origem->getSlug() !== $request->slug) {
            if ($this->origemRepository->buscarPorSlug($request->slug, $mundoId)) {
                return response()->json([
                    'message' => 'Já existe uma origem com este slug neste mundo'
                ], Response::HTTP_CONFLICT);
            }
        }

        // Atualiza os campos modificados
        if ($request->has('slug')) {
            $novaOrigem = new Origem(
                $mundoId,
                $request->slug,
                $request->has('nome') ? $request->nome : $origem->getNome(),
                $request->has('descricao') ? $request->descricao : $origem->getDescricao()
            );
        } else {
            $novaOrigem = new Origem(
                $mundoId,
                $origem->getSlug(),
                $request->has('nome') ? $request->nome : $origem->getNome(),
                $request->has('descricao') ? $request->descricao : $origem->getDescricao()
            );
        }

        $novaOrigem->setId($origem->getId());
        $this->origemRepository->atualizar($novaOrigem);

        return response()->json($novaOrigem);
    }

    public function excluir(int $mundoId, int $id)
    {
        $origem = $this->origemRepository->buscarPorId($id, $mundoId);

        if (!$origem) {
            return response()->json(['message' => 'Origem não encontrada'], Response::HTTP_NOT_FOUND);
        }

        if ($this->origemRepository->possuiPersonagens($id)) {
            return response()->json([
                'message' => 'Não é possível excluir uma origem que possui personagens vinculados'
            ], Response::HTTP_CONFLICT);
        }

        $this->origemRepository->excluir($id, $mundoId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function criarEfeito(Request $request, int $mundoId, int $origemId)
    {
        $validator = Validator::make($request->all(), [
            'tipo' => 'required|string|in:delta_atributo,conceder_item,conceder_habilidade,custom',
            'atributo_id' => 'required_if:tipo,delta_atributo|integer|exists:atributos,id',
            'delta' => 'required_if:tipo,delta_atributo|integer',
            'notas' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $origem = $this->origemRepository->buscarPorId($origemId, $mundoId);

        if (!$origem) {
            return response()->json(['message' => 'Origem não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $efeito = new OrigemEfeito(
            $request->tipo,
            $request->atributo_id,
            $request->delta,
            $request->notas ? json_decode($request->notas, true) : null
        );

        $efeitos = [];
        $efeitos[] = $efeito;
        $this->origemRepository->vincularEfeitos($origemId, $efeitos);

        return response()->json($efeito, Response::HTTP_CREATED);
    }

    public function listarEfeitos(int $mundoId, int $origemId)
    {
        $origem = $this->origemRepository->buscarPorId($origemId, $mundoId);

        if (!$origem) {
            return response()->json(['message' => 'Origem não encontrada'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($origem->getEfeitos());
    }

    public function atualizarEfeito(Request $request, int $mundoId, int $origemId, int $efeitoId)
    {
        $validator = Validator::make($request->all(), [
            'tipo' => 'string|in:delta_atributo,conceder_item,conceder_habilidade,custom',
            'atributo_id' => 'required_if:tipo,delta_atributo|integer|exists:atributos,id',
            'delta' => 'required_if:tipo,delta_atributo|integer',
            'notas' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $origem = $this->origemRepository->buscarPorId($origemId, $mundoId);

        if (!$origem) {
            return response()->json(['message' => 'Origem não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $efeito = new OrigemEfeito(
            $request->tipo,
            $request->atributo_id,
            $request->delta,
            $request->notas ? json_decode($request->notas, true) : null
        );
        $efeito->setId($efeitoId);

        $efeitos = $origem->getEfeitos();
        foreach ($efeitos as $key => $existingEfeito) {
            if ($existingEfeito->getId() === $efeitoId) {
                $efeitos[$key] = $efeito;
                break;
            }
        }

        $this->origemRepository->atualizarEfeitos($origemId, $efeitos);

        return response()->json($efeito);
    }

    public function excluirEfeito(int $mundoId, int $origemId, int $efeitoId)
    {
        $origem = $this->origemRepository->buscarPorId($origemId, $mundoId);

        if (!$origem) {
            return response()->json(['message' => 'Origem não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $efeitos = array_filter($origem->getEfeitos(), function ($efeito) use ($efeitoId) {
            return $efeito->getId() !== $efeitoId;
        });

        $this->origemRepository->atualizarEfeitos($origemId, $efeitos);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
