<?php

namespace App\Http\Controllers;

use App\UseCases\Campanha\AtualizarCampanhaUseCase;
use App\UseCases\Campanha\CriarCampanhaUseCase;
use App\UseCases\Campanha\ExcluirCampanhaUseCase;
use App\Repositories\Interfaces\CampanhaRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CampanhaController extends Controller
{
    private CampanhaRepositoryInterface $campanhaRepository;
    private CriarCampanhaUseCase $criarCampanha;
    private AtualizarCampanhaUseCase $atualizarCampanha;
    private ExcluirCampanhaUseCase $excluirCampanha;

    public function __construct(
        CampanhaRepositoryInterface $campanhaRepository,
        CriarCampanhaUseCase $criarCampanha,
        AtualizarCampanhaUseCase $atualizarCampanha,
        ExcluirCampanhaUseCase $excluirCampanha
    ) {
        $this->campanhaRepository = $campanhaRepository;
        $this->criarCampanha = $criarCampanha;
        $this->atualizarCampanha = $atualizarCampanha;
        $this->excluirCampanha = $excluirCampanha;
    }

    public function criar(Request $request, int $mundoId)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio'
        ]);

        $campanha = $this->criarCampanha->executar(
            $mundoId,
            $request->input('nome'),
            Auth::id(),
            $request->input('descricao'),
            $request->input('data_inicio'),
            $request->input('data_fim')
        );

        return response()->json($campanha, Response::HTTP_CREATED);
    }

    public function atualizar(Request $request, int $mundoId, int $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio'
        ]);

        $this->atualizarCampanha->executar(
            $id,
            $mundoId,
            $request->input('nome'),
            $request->input('descricao'),
            $request->input('data_inicio'),
            $request->input('data_fim')
        );

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function excluir(int $mundoId, int $id)
    {
        $this->excluirCampanha->executar($id, $mundoId);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function buscarPorId(int $mundoId, int $id)
    {
        $campanha = $this->campanhaRepository->buscarPorId($id);

        if (!$campanha || $campanha->getMundoId() !== $mundoId) {
            return response()->json(['message' => 'Campanha não encontrada'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($campanha);
    }

    public function listar(int $mundoId)
    {
        $campanhas = $this->campanhaRepository->listarPorMundo($mundoId);
        return response()->json($campanhas);
    }
}
