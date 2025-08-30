<?php

namespace App\Http\Controllers;

use App\UseCases\Mundo\AdicionarMembroUseCase;
use App\UseCases\Mundo\AtualizarRegrasMundoUseCase;
use App\UseCases\Mundo\CriarMundoUseCase;
use App\UseCases\Mundo\ListarMundosUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;

class MundoController extends Controller
{
    private CriarMundoUseCase $criarMundoUseCase;
    private AdicionarMembroUseCase $adicionarMembroUseCase;
    private AtualizarRegrasMundoUseCase $atualizarRegrasMundoUseCase;
    private ListarMundosUseCase $listarMundosUseCase;

    public function __construct(
        CriarMundoUseCase $criarMundoUseCase,
        AdicionarMembroUseCase $adicionarMembroUseCase,
        AtualizarRegrasMundoUseCase $atualizarRegrasMundoUseCase,
        ListarMundosUseCase $listarMundosUseCase
    ) {
        $this->criarMundoUseCase = $criarMundoUseCase;
        $this->adicionarMembroUseCase = $adicionarMembroUseCase;
        $this->atualizarRegrasMundoUseCase = $atualizarRegrasMundoUseCase;
        $this->listarMundosUseCase = $listarMundosUseCase;
    }

    public function criar(Request $request): JsonResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string'
        ]);

        try {
            $mundo = $this->criarMundoUseCase->executar(
                $request->input('nome'),
                $request->input('descricao'),
                $request->auth['sub'], // id do usuário
            );

            $encryptedMundoId = Crypt::encryptString($mundo->getId());
            return response()->json([
                'id' => $encryptedMundoId,
                'nome' => $mundo->getNome(),
                'descricao' => $mundo->getDescricao(),
                'criado_por' => $mundo->getCriadoPor(),
                'criado_em' => $mundo->getCriadoEm()->format('Y-m-d H:i:s')
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function listar(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'nullable|int',
            'offset' => 'nullable|int'
        ]);

        try {
            $mundos = $this->listarMundosUseCase->executar(
                $request->input('limit') ?? 10,
                $request->input('offset') ?? 0,
                $request->auth['sub'], // id do usuário
            );

            return response()->json($mundos, Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function adicionarMembro(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'usuario_id' => 'required|integer|exists:usuarios,id',
            'papel' => 'required|string|in:admin,mestre,jogador'
        ]);

        try {
            $this->adicionarMembroUseCase->execute(
                $id,
                $request->input('usuario_id'),
                $request->input('papel')
            );

            return response()->json(['message' => 'Membro adicionado com sucesso']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function atualizarRegras(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'pontos_base_por_personagem' => 'required|integer|min:0',
            'niveis_dado_por_personagem' => 'required|integer|min:0',
            'sequencia_dados' => 'required|array',
            'sequencia_dados.*' => 'integer|in:4,6,8,10,12,20',
            'limite_max_tipo_dado_id' => 'nullable|integer|exists:tipos_dado,id',
            'permite_pvp' => 'required|boolean',
            'permite_pve' => 'required|boolean'
        ]);

        try {
            $this->atualizarRegrasMundoUseCase->execute(
                $id,
                $request->input('pontos_base_por_personagem'),
                $request->input('niveis_dado_por_personagem'),
                $request->input('sequencia_dados'),
                $request->input('limite_max_tipo_dado_id'),
                $request->input('permite_pvp'),
                $request->input('permite_pve')
            );

            return response()->json(['message' => 'Regras atualizadas com sucesso']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
