<?php

namespace App\Http\Controllers;

use App\Domain\Atributo\Atributo;
use App\UseCases\Atributo\CriarAtributoUseCase;
use App\Repositories\Interfaces\AtributoRepositoryInterface;
use App\UseCases\Atributo\ListarTipoDados;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AtributoController extends Controller
{
    private AtributoRepositoryInterface $atributoRepository;
    private CriarAtributoUseCase $criarAtributo;
    private ListarTipoDados $listarTipoDados;

    public function __construct(
        AtributoRepositoryInterface $atributoRepository,
        CriarAtributoUseCase $criarAtributo,
        ListarTipoDados $listarTipoDados
    ) {
        $this->atributoRepository = $atributoRepository;
        $this->criarAtributo = $criarAtributo;
        $this->listarTipoDados = $listarTipoDados;
    }

    public function criar(Request $request, int $mundoId)
    {
        $request->validate([
            'chave' => 'required|string|regex:/^[a-z0-9_]+$/',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'exibir' => 'boolean'
        ]);

        // Auth::id(),
        $atributo = $this->criarAtributo->executar(
            $mundoId,
            $request->input('chave'),
            $request->input('nome'),
            $request->input('descricao'),
            $request->input('exibir', true)
        );

        return response()->json($atributo, Response::HTTP_CREATED);
    }

    public function atualizar(Request $request, int $mundoId, int $id)
    {
        $atributo = $this->atributoRepository->buscarPorId($id, $mundoId);
        if (!$atributo) {
            return response()->json(['message' => 'Atributo não encontrado'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'exibir' => 'boolean'
        ]);

        if ($request->has('chave') && $request->input('chave') !== $atributo->getChave()) {
            $atributoExistente = $this->atributoRepository->buscarPorChave($request->input('chave'), $mundoId);
            if ($atributoExistente) {
                return response()->json(['message' => 'Já existe um atributo com essa chave'], Response::HTTP_CONFLICT);
            }
        }

        // Auth::id(),
        $novoAtributo = new Atributo(
            $mundoId,
            $request->input('chave', $atributo->getChave()),
            $request->input('nome', $atributo->getNome()),
            $request->input('descricao', $atributo->getDescricao()),
            $request->input('exibir', $atributo->isExibir())
        );

        $novoAtributo->setId($id);
        $this->atributoRepository->atualizar($novoAtributo);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function excluir(int $mundoId, int $id)
    {
        $atributo = $this->atributoRepository->buscarPorId($id, $mundoId);
        if (!$atributo) {
            return response()->json(['message' => 'Atributo não encontrado'], Response::HTTP_NOT_FOUND);
        }

        if ($this->atributoRepository->possuiDependencias($id)) {
            return response()->json(
                ['message' => 'Não é possível excluir o atributo pois ele possui dependências'],
                Response::HTTP_CONFLICT
            );
        }

        $this->atributoRepository->excluir($id, $mundoId);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function listar(Request $request, int $mundoId)
    {
        $offset = $request->query('offset', 0);
        $atributos = $this->atributoRepository->listarPorMundo($mundoId, $offset);
        return response()->json($atributos, Response::HTTP_OK);
    }

    public function buscarPorId(int $mundoId, int $id)
    {
        $atributo = $this->atributoRepository->buscarPorId($id, $mundoId);

        if (!$atributo) {
            return response()->json(['message' => 'Atributo não encontrado'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($atributo);
    }

    public function listarTipoDados()
    {
        $tipos = $this->listarTipoDados->executar();
        return response()->json($tipos, Response::HTTP_OK);
    }
}
