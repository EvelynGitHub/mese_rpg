<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\HabilidadeRepositoryInterface;
use App\UseCases\Classe\AdicionarAtributoClasseUseCase;
use App\UseCases\Classe\AtualizarClasse;
use App\UseCases\Classe\CriarClasseUseCase;
use App\Repositories\Interfaces\ClasseRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ClasseController extends Controller
{
    private ClasseRepositoryInterface $classeRepository;
    private HabilidadeRepositoryInterface $habilidadeRepositoryInterface;
    private CriarClasseUseCase $criarClasse;
    private AdicionarAtributoClasseUseCase $adicionarAtributo;
    private AtualizarClasse $atualizarClasse;

    public function __construct(
        ClasseRepositoryInterface $classeRepository,
        CriarClasseUseCase $criarClasse,
        AtualizarClasse $atualizarClasse,
        AdicionarAtributoClasseUseCase $adicionarAtributo,
        HabilidadeRepositoryInterface $habilidadeRepositoryInterface
    ) {
        $this->classeRepository = $classeRepository;
        $this->criarClasse = $criarClasse;
        $this->adicionarAtributo = $adicionarAtributo;
        $this->habilidadeRepositoryInterface = $habilidadeRepositoryInterface;
        $this->atualizarClasse = $atualizarClasse;
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
            // garante que 'habilidades' é um array
            'habilidades' => 'nullable|array',
            // valida cada item do array
            'habilidades.*.habilidade_id' => 'required|integer',
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

        // Por enquanto, futuramente mover para caso de uso próprio
        $habilidades = $request->input('habilidades', []);
        $habilidadesIds = array_column($habilidades, 'habilidade_id');

        foreach ($habilidadesIds as $id) {
            $this->habilidadeRepositoryInterface->vincularClasse($id, $classe->getId());
        }

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
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'atributos' => 'nullable|array',
            'atributos.*.atributo_id' => 'required|integer',
            'atributos.*.tipo_dado_id' => 'nullable|integer',
            'atributos.*.base_fixa' => 'integer|min:0',
            'atributos.*.limite_base_fixa' => 'nullable|integer|min:0',
            'atributos.*.limite_tipo_dado_id' => 'nullable|integer',
            'atributos.*.imutavel' => 'boolean',
            'habilidades' => 'nullable|array',
            'habilidades.*.habilidade_id' => 'required|integer',
        ]);

        $this->atualizarClasse->executar($mundoId, $id, $dados);

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
