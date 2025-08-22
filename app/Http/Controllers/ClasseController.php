<?php

namespace App\Http\Controllers;

use App\UseCases\Classe\AdicionarAtributoClasseUseCase;
use App\UseCases\Classe\CriarClasseUseCase;
use App\Repositories\Interfaces\ClasseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ClasseController extends Controller
{
    private ClasseRepositoryInterface $classeRepository;
    private CriarClasseUseCase $criarClasse;
    private AdicionarAtributoClasseUseCase $adicionarAtributo;

    public function __construct(
        ClasseRepositoryInterface $classeRepository,
        CriarClasseUseCase $criarClasse,
        AdicionarAtributoClasseUseCase $adicionarAtributo
    ) {
        $this->classeRepository = $classeRepository;
        $this->criarClasse = $criarClasse;
        $this->adicionarAtributo = $adicionarAtributo;
    }

    public function criar(Request $request, int $mundoId)
    {
        $request->validate([
            'slug' => 'required|string|regex:/^[a-z0-9\-]+$/',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string'
        ]);

        $classe = $this->criarClasse->executar(
            $mundoId,
            $request->input('slug'),
            $request->input('nome'),
            Auth::id(),
            $request->input('descricao')
        );

        return response()->json($classe, Response::HTTP_CREATED);
    }

    public function adicionarAtributo(Request $request, int $mundoId, int $classeId)
    {
        $request->validate([
            'atributo_id' => 'required|integer',
            'tipo_dado_id' => 'nullable|integer',
            'base_fixa' => 'integer|min:0',
            'limite_base_fixa' => 'nullable|integer|min:0',
            'limite_tipo_dado_id' => 'nullable|integer',
            'imutavel' => 'boolean'
        ]);

        $classeAtributo = $this->adicionarAtributo->executar(
            $mundoId,
            $classeId,
            $request->input('atributo_id'),
            $request->input('tipo_dado_id'),
            $request->input('base_fixa', 0),
            $request->input('limite_base_fixa'),
            $request->input('limite_tipo_dado_id'),
            $request->input('imutavel', false)
        );

        return response()->json($classeAtributo, Response::HTTP_CREATED);
    }

    public function atualizar(Request $request, int $mundoId, int $id)
    {
        $classe = $this->classeRepository->buscarPorId($id, $mundoId);
        if (!$classe) {
            return response()->json(['message' => 'Classe não encontrada'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string'
        ]);

        $classe->setNome($request->input('nome'));
        $classe->setDescricao($request->input('descricao'));

        $this->classeRepository->atualizar($classe);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function excluir(int $mundoId, int $id)
    {
        $classe = $this->classeRepository->buscarPorId($id, $mundoId);
        if (!$classe) {
            return response()->json(['message' => 'Classe não encontrada'], Response::HTTP_NOT_FOUND);
        }

        if ($this->classeRepository->possuiPersonagens($id)) {
            return response()->json(
                ['message' => 'Não é possível excluir a classe pois existem personagens vinculados'],
                Response::HTTP_CONFLICT
            );
        }

        $this->classeRepository->excluir($id,  $mundoId);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function listar(int $mundoId)
    {
        $classes = $this->classeRepository->listarPorMundo($mundoId);
        return response()->json($classes);
    }

    public function buscarPorId(int $mundoId, int $id)
    {
        $classe = $this->classeRepository->buscarPorId($id, $mundoId);

        if (!$classe) {
            return response()->json(['message' => 'Classe não encontrada'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($classe);
    }
}
