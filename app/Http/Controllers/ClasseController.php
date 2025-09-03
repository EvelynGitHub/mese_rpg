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
            'descricao' => 'nullable|string',
            // garante que 'atributos' é um array
            'atributos' => 'required|array',
            // valida cada item do array
            'atributos.*.atributo_id' => 'required|integer',
            'atributos.*.tipo_dado_id' => 'nullable|integer',
            'atributos.*.base_fixa' => 'integer|min:0',
            'atributos.*.limite_base_fixa' => 'nullable|integer|min:0',
            'atributos.*.limite_tipo_dado_id' => 'nullable|integer',
            'atributos.*.imutavel' => 'boolean',
        ]);

        $classe = $this->criarClasse->executar(
            $mundoId,
            $request->input('slug'),
            $request->input('nome'),
            $request->auth['sub'],
            $request->input('descricao')
        );

        $this->adicionarAtributo->executar(
            $mundoId,
            $classe->getId(),
            $request->input('atributos', []),
            $request->auth['sub']
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

        $atributos = $request->all();

        $classeAtributo = $this->adicionarAtributo->executar(
            $mundoId,
            $classeId,
            [$atributos],
            $request->auth['sub']
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

        $this->classeRepository->excluir($id, $mundoId);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function listar(Request $request, int $mundoId)
    {
        $offset = $request->query('offset', 0);
        $classes = $this->classeRepository->listarPorMundo($mundoId, $offset);
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
