<?php

namespace App\Http\Controllers;

use App\Domain\Habilidade\Habilidade;
use App\Repositories\Interfaces\HabilidadeRepositoryInterface;
use App\Repositories\Interfaces\ClasseRepositoryInterface;
use App\Repositories\Interfaces\OrigemRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class HabilidadeController extends Controller
{
    private $habilidadeRepository;
    private $classeRepository;
    private $origemRepository;

    public function __construct(
        HabilidadeRepositoryInterface $habilidadeRepository,
        ClasseRepositoryInterface $classeRepository,
        OrigemRepositoryInterface $origemRepository
    ) {
        $this->habilidadeRepository = $habilidadeRepository;
        $this->classeRepository = $classeRepository;
        $this->origemRepository = $origemRepository;
    }

    public function criar(Request $request, int $mundoId)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:255',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'bonus' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Verifica se já existe habilidade com mesmo slug no mundo
        if ($this->habilidadeRepository->buscarPorSlug($request->slug, $mundoId)) {
            return response()->json([
                'message' => 'Já existe uma habilidade com este slug neste mundo'
            ], Response::HTTP_CONFLICT);
        }

        try {
            $habilidade = new Habilidade(
                $mundoId,
                $request->slug,
                $request->nome,
                $request->descricao,
                $request->bonus
            );

            $habilidade = $this->habilidadeRepository->criar($habilidade);
            return response()->json($habilidade, Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function listar(int $mundoId)
    {
        $habilidades = $this->habilidadeRepository->listarPorMundo($mundoId);
        return response()->json($habilidades);
    }

    public function buscar(int $mundoId, int $id)
    {
        $habilidade = $this->habilidadeRepository->buscarPorId($id, $mundoId);

        if (!$habilidade) {
            return response()->json(['message' => 'Habilidade não encontrada'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($habilidade);
    }

    public function atualizar(Request $request, int $mundoId, int $id)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'string|max:255',
            'nome' => 'string|max:255',
            'descricao' => 'nullable|string',
            'bonus' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $habilidade = $this->habilidadeRepository->buscarPorId($id, $mundoId);

        if (!$habilidade) {
            return response()->json(['message' => 'Habilidade não encontrada'], Response::HTTP_NOT_FOUND);
        }

        if ($request->has('slug') && $habilidade->getSlug() !== $request->slug) {
            if ($this->habilidadeRepository->buscarPorSlug($request->slug, $mundoId)) {
                return response()->json([
                    'message' => 'Já existe uma habilidade com este slug neste mundo'
                ], Response::HTTP_CONFLICT);
            }
        }

        try {
            $novaHabilidade = new Habilidade(
                $mundoId,
                $request->slug ?? $habilidade->getSlug(),
                $request->nome ?? $habilidade->getNome(),
                $request->descricao ?? $habilidade->getDescricao(),
                $request->bonus ?? $habilidade->getBonus(),
                $habilidade->isAtiva()
            );

            $novaHabilidade->setId($habilidade->getId());
            $this->habilidadeRepository->atualizar($novaHabilidade);

            return response()->json($novaHabilidade);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function excluir(int $mundoId, int $id)
    {
        $habilidade = $this->habilidadeRepository->buscarPorId($id, $mundoId);

        if (!$habilidade) {
            return response()->json(['message' => 'Habilidade não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $this->habilidadeRepository->excluir($id, $mundoId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function vincularClasse(Request $request, int $mundoId, int $classeId, int $habilidadeId)
    {
        $classe = $this->classeRepository->buscarPorId($classeId, $mundoId);
        if (!$classe) {
            return response()->json(['message' => 'Classe não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $habilidade = $this->habilidadeRepository->buscarPorId($habilidadeId, $mundoId);
        if (!$habilidade) {
            return response()->json(['message' => 'Habilidade não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $resultado = $this->habilidadeRepository->vincularClasse($habilidadeId, $classeId);
        if (!$resultado) {
            return response()->json([
                'message' => 'A habilidade já está vinculada a esta classe'
            ], Response::HTTP_CONFLICT);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function vincularOrigem(Request $request, int $mundoId, int $origemId, int $habilidadeId)
    {
        $origem = $this->origemRepository->buscarPorId($origemId, $mundoId);
        if (!$origem) {
            return response()->json(['message' => 'Origem não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $habilidade = $this->habilidadeRepository->buscarPorId($habilidadeId, $mundoId);
        if (!$habilidade) {
            return response()->json(['message' => 'Habilidade não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $resultado = $this->habilidadeRepository->vincularOrigem($habilidadeId, $origemId);
        if (!$resultado) {
            return response()->json([
                'message' => 'A habilidade já está vinculada a esta origem'
            ], Response::HTTP_CONFLICT);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function desvincularClasse(int $mundoId, int $classeId, int $habilidadeId)
    {
        $classe = $this->classeRepository->buscarPorId($classeId, $mundoId);
        if (!$classe) {
            return response()->json(['message' => 'Classe não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $habilidade = $this->habilidadeRepository->buscarPorId($habilidadeId, $mundoId);
        if (!$habilidade) {
            return response()->json(['message' => 'Habilidade não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $this->habilidadeRepository->desvincularClasse($habilidadeId, $classeId);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function desvincularOrigem(int $mundoId, int $origemId, int $habilidadeId)
    {
        $origem = $this->origemRepository->buscarPorId($origemId, $mundoId);
        if (!$origem) {
            return response()->json(['message' => 'Origem não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $habilidade = $this->habilidadeRepository->buscarPorId($habilidadeId, $mundoId);
        if (!$habilidade) {
            return response()->json(['message' => 'Habilidade não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $this->habilidadeRepository->desvincularOrigem($habilidadeId, $origemId);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function listarPorClasse(int $mundoId, int $classeId)
    {
        $classe = $this->classeRepository->buscarPorId($classeId, $mundoId);
        if (!$classe) {
            return response()->json(['message' => 'Classe não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $habilidades = $this->habilidadeRepository->listarPorClasse($classeId);
        return response()->json($habilidades);
    }

    public function listarPorOrigem(int $mundoId, int $origemId)
    {
        $origem = $this->origemRepository->buscarPorId($origemId, $mundoId);
        if (!$origem) {
            return response()->json(['message' => 'Origem não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $habilidades = $this->habilidadeRepository->listarPorOrigem($origemId);
        return response()->json($habilidades);
    }
}
