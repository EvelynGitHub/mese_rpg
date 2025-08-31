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
    private HabilidadeRepositoryInterface $habilidadeRepository;
    private ClasseRepositoryInterface $classeRepository;
    private OrigemRepositoryInterface $origemRepository;

    public function __construct(
        HabilidadeRepositoryInterface $habilidadeRepository,
        ClasseRepositoryInterface $classeRepository,
        OrigemRepositoryInterface $origemRepository
    ) {
        $this->habilidadeRepository = $habilidadeRepository;
        $this->classeRepository = $classeRepository;
        $this->origemRepository = $origemRepository;
    }

    public function criar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:255',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'bonus' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Verifica se já existe habilidade com mesmo slug no mundo
        if ($this->habilidadeRepository->buscarPorSlug($request->slug, $request->mundoId)) {
            return response()->json([
                'message' => 'Já existe uma habilidade com este slug neste mundo'
            ], Response::HTTP_CONFLICT);
        }

        try {
            $habilidade = new Habilidade(
                $request->mundoId,
                $request->slug,
                $request->nome,
                $request->descricao,
                json_decode($request->bonus, true)
            );

            $habilidade = $this->habilidadeRepository->criar($habilidade);
            return response()->json($habilidade, Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function listar(int $mundoId, Request $request)
    {
        $offset = $request->query('offset', 0);
        $habilidades = $this->habilidadeRepository->listarPorMundo($mundoId, $offset);

        $habilidadesA = array_map(
            fn($habilidade) =>
            [
                'id' => $habilidade->getId(),
                'slug' => $habilidade->getSlug(),
                'nome' => $habilidade->getNome(),
                'descricao' => $habilidade->getDescricao(),
                'bonus' => json_encode($habilidade->getBonus()),
                'ativa' => $habilidade->isAtiva(),
            ]
            ,
            $habilidades
        );

        return response()->json($habilidadesA, 200);
    }

    public function buscar(int $mundoId, int $id)
    {
        $habilidade = $this->habilidadeRepository->buscarPorId($id, $mundoId);

        if (!$habilidade) {
            return response()->json(['message' => 'Habilidade não encontrada'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'id' => $habilidade->getId(),
            'slug' => $habilidade->getSlug(),
            'nome' => $habilidade->getNome(),
            'descricao' => $habilidade->getDescricao(),
            'bonus' => empty($habilidade->getBonus()) ? null : json_encode($habilidade->getBonus()),
            'ativa' => $habilidade->isAtiva(),
        ], Response::HTTP_OK);
    }

    public function atualizar(Request $request, int $mundoId, int $id)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'string|max:255',
            'nome' => 'string|max:255',
            'descricao' => 'nullable|string',
            'bonus' => 'nullable|json'
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

        $bonus = $habilidade->getBonus();

        if ($request->has('bonus') && is_string($request->bonus)) {
            $bonus = json_decode($request->bonus, true);
        }

        try {

            $novaHabilidade = new Habilidade(
                $request->mundoId,
                $request->slug ?? $habilidade->getSlug(),
                $request->nome ?? $habilidade->getNome(),
                $request->descricao ?? $habilidade->getDescricao(),
                $bonus,
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

        return response()->json(['message' => 'Habilidade excluída.'], Response::HTTP_NO_CONTENT);
    }

    public function vincularClasse(int $mundoId, int $classeId, int $habilidadeId)
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

    public function vincularOrigem(int $mundoId, int $origemId, int $habilidadeId)
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
