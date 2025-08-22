<?php

namespace App\Http\Controllers;
use App\Domain\Personagem\CalculoAtributosService;
use App\Repositories\Interfaces\PersonagemRepositoryInterface;
use App\Domain\Personagem\Personagem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PersonagemController extends Controller
{
    private PersonagemRepositoryInterface $personagemRepository;

    public function __construct(PersonagemRepositoryInterface $personagemRepository)
    {
        $this->personagemRepository = $personagemRepository;
    }

    public function criar(Request $request, int $mundoId): JsonResponse
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'usuario_id' => 'nullable|exists:users,id',
            'campanha_id' => [
                'nullable',
                'exists:campanhas,id,mundo_id,' . $mundoId
            ],
            'classe_id' => [
                'required',
                'exists:classes,id,mundo_id,' . $mundoId
            ],
            'origem_id' => [
                'nullable',
                'exists:origens,id,mundo_id,' . $mundoId
            ],
            'nome' => 'required|string|max:255',
            'pontos_base_map' => 'required|array',
            'niveis_dado' => 'required|array',
            'atributos_override' => 'nullable|array',
            'inventario' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verificar se usuário tem permissão para criar personagem para outro usuário
        $usuarioId = $request->input('usuario_id', $user->id);
        if ($usuarioId !== $user->id) {
            $papel = DB::table('usuarios_mundos')
                ->where('mundo_id', $mundoId)
                ->where('usuario_id', $user->id)
                ->value('papel');

            if (!in_array($papel, ['admin', 'mestre'])) {
                return response()->json(['message' => 'Sem permissão para criar personagem para outro usuário'], 403);
            }
        }

        // Validar regras do mundo e da classe
        $regras = DB::table('mundos_regras')->where('mundo_id', $mundoId)->first();
        $X = $regras->pontos_base_por_personagem ?? $request->input('X') ?? 0;
        $Y = $regras->niveis_dado_por_personagem ?? $request->input('Y') ?? 0;

        if (!$X || !$Y) {
            return response()->json(['message' => 'X e Y são obrigatórios quando não definidos nas regras do mundo'], 422);
        }

        // Validar pontos base e níveis de dado
        $pontosBaseMap = $request->input('pontos_base_map', []);
        $somaPontosBase = array_sum($pontosBaseMap);

        if ($somaPontosBase > $X) {
            return response()->json(['message' => 'Total de pontos base excede o limite permitido'], 409);
        }

        $somaNiveisDado = array_sum($request->input('niveis_dado', []));
        if ($somaNiveisDado > $Y) {
            return response()->json(['message' => 'Total de níveis de dado excede o limite permitido'], 409);
        }

        // Validar atributos imutáveis
        $classeAtributos = DB::table('classes_atributos')
            ->where('classe_id', $request->input('classe_id'))
            ->where('imutavel', true)
            ->get();

        foreach ($classeAtributos as $atributo) {
            if (isset($request->input('niveis_dado')[$atributo->atributo_id])) {
                return response()->json(['message' => 'Não é permitido distribuir níveis em atributos imutáveis'], 409);
            }
        }

        try {
            $personagem = new Personagem(
                $request->input('nome'),
                $mundoId,
                $usuarioId,
                $request->input('classe_id'),
                $request->input('descricao'),
                $request->input('idade'),
                $request->input('campanha_id'),
                $request->input('origem_id'),
                $X - $somaPontosBase,
                $pontosBaseMap,
                $request->input('niveis_dado'),
                $request->input('atributos_override'),
                $request->input('inventario', [])
            );

            $personagemCriado = $this->personagemRepository->criar($personagem);
            return response()->json($personagemCriado, 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar personagem: ' . $e->getMessage()], 500);
        }
    }

    public function listar(Request $request, int $mundoId): JsonResponse
    {
        $user = auth()->user();
        $papel = DB::table('usuarios_mundos')
            ->where('mundo_id', $mundoId)
            ->where('usuario_id', $user->id)
            ->value('papel');

        // Se não for admin/mestre, lista apenas os próprios personagens
        $usuarioId = in_array($papel, ['admin', 'mestre']) ? null : $user->id;

        $personagens = $this->personagemRepository->listarPorMundo($mundoId, $usuarioId);

        if ($request->query('com_calculo') == '1') {
            // TODO: Implementar cálculo de atributos para cada personagem
            // Incluir valor efetivo e dados atualizados
        }

        return response()->json($personagens);
    }

    public function buscar(Request $request, int $mundoId, int $id): JsonResponse
    {
        $user = auth()->user();
        $personagem = $this->personagemRepository->buscarPorId($id, $mundoId);

        if (!$personagem) {
            return response()->json(['message' => 'Personagem não encontrado'], 404);
        }

        // Verificar permissão
        $papel = DB::table('usuarios_mundos')
            ->where('mundo_id', $mundoId)
            ->where('usuario_id', $user->id)
            ->value('papel');

        if (!in_array($papel, ['admin', 'mestre']) && $personagem->getUsuarioId() !== $user->id) {
            return response()->json(['message' => 'Sem permissão para visualizar este personagem'], 403);
        }

        if ($request->query('com_calculo') == '1') {
            $calculoService = new CalculoAtributosService();
            $atributosFinais = $calculoService->calcular($personagem);

            return response()->json([
                'personagem' => $personagem,
                'atributos' => $atributosFinais
            ]);

        }

        return response()->json($personagem);
    }

    public function atualizar(Request $request, int $mundoId, int $id): JsonResponse
    {
        $user = auth()->user();
        $personagem = $this->personagemRepository->buscarPorId($id, $mundoId);

        if (!$personagem) {
            return response()->json(['message' => 'Personagem não encontrado'], 404);
        }

        // Verificar permissão
        $papel = DB::table('usuarios_mundos')
            ->where('mundo_id', $mundoId)
            ->where('usuario_id', $user->id)
            ->value('papel');

        $ehDono = $personagem->getUsuarioId() === $user->id;
        $ehAdminOuMestre = in_array($papel, ['admin', 'mestre']);

        if (!$ehDono && !$ehAdminOuMestre) {
            return response()->json(['message' => 'Sem permissão para editar este personagem'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nome' => 'sometimes|required|string|max:255',
            'campanha_id' => [
                'nullable',
                'exists:campanhas,id,mundo_id,' . $mundoId
            ],
            'pontos_base_map' => 'sometimes|required|array',
            'niveis_dado' => 'sometimes|required|array',
            'atributos_override' => $ehAdminOuMestre ? 'nullable|array' : 'prohibited',
            'inventario' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validar limites de pontos e níveis apenas se foram fornecidos
        if ($request->has('pontos_base_map') || $request->has('niveis_dado')) {
            $regras = DB::table('mundos_regras')->where('mundo_id', $mundoId)->first();

            if ($request->has('pontos_base_map')) {
                $somaPontosBase = array_sum($request->input('pontos_base_map', []));
                if ($somaPontosBase > $regras->pontos_base_por_personagem || $somaPontosBase < $personagem->getPontosBase()) {
                    return response()->json(['message' => 'Total de pontos base excede o limite permitido'], 409);
                }
            }

            if ($request->has('niveis_dado')) {
                $somaNiveisDado = array_sum($request->input('niveis_dado', []));
                if ($somaNiveisDado > $regras->niveis_dado_por_personagem) {
                    return response()->json(['message' => 'Total de níveis de dado excede o limite permitido'], 409);
                }
            }

            // Validar atributos imutáveis
            $classeAtributos = DB::table('classes_atributos')
                ->where('classe_id', $personagem->getClasseId())
                ->where('imutavel', true)
                ->get();

            foreach ($classeAtributos as $atributo) {
                if ($request->has('niveis_dado') && isset($request->input('niveis_dado')[$atributo->atributo_id])) {
                    return response()->json(['message' => 'Não é permitido distribuir níveis em atributos imutáveis'], 409);
                }
            }
        }

        // Atualizar apenas os campos fornecidos
        if ($request->has('nome')) {
            $personagem->setNome($request->input('nome'));
        }
        if ($request->has('campanha_id')) {
            $personagem->setCampanhaId($request->input('campanha_id'));
        }
        if ($request->has('pontos_base_map')) {
            $map = $request->input('pontos_base_map');
            $personagem->setPontosBaseMap($map);
            // $personagem->setPontosBase(array_sum($map)); // resumo
        }
        if ($request->has('niveis_dado')) {
            $personagem->setNiveisDado($request->input('niveis_dado'));
        }
        if ($ehAdminOuMestre && $request->has('atributos_override')) {
            $personagem->setAtributosOverride($request->input('atributos_override'));
        }
        if ($request->has('inventario')) {
            $personagem->setInventario($request->input('inventario'));
        }

        if ($this->personagemRepository->atualizar($personagem)) {
            // 🔥 Chamar o serviço de cálculo
            $calculoService = new CalculoAtributosService();
            $atributosFinais = $calculoService->calcular($personagem);

            return response()->json([
                'personagem' => $personagem,
                'atributos' => $atributosFinais
            ]);
        }

        return response()->json(['message' => 'Erro ao atualizar personagem'], 500);
    }

    public function resetarAlocacao(int $mundoId, int $id): JsonResponse
    {
        $user = auth()->user();
        $papel = DB::table('usuarios_mundos')
            ->where('mundo_id', $mundoId)
            ->where('usuario_id', $user->id)
            ->value('papel');

        if (!in_array($papel, ['admin', 'mestre'])) {
            return response()->json(['message' => 'Apenas mestres e administradores podem resetar alocação'], 403);
        }

        if ($this->personagemRepository->resetarAlocacao($id, $mundoId)) {
            return response()->json(['message' => 'Alocação resetada com sucesso']);
        }

        return response()->json(['message' => 'Personagem não encontrado'], 404);
    }

    public function equiparItem(Request $request, int $mundoId, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => [
                'required',
                'exists:itens,id,mundo_id,' . $mundoId
            ],
            'quantidade' => 'integer|min:1|default:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $personagem = $this->personagemRepository->buscarPorId($id, $mundoId);

        if (!$personagem) {
            return response()->json(['message' => 'Personagem não encontrado'], 404);
        }

        // Verificar permissão
        $papel = DB::table('usuarios_mundos')
            ->where('mundo_id', $mundoId)
            ->where('usuario_id', $user->id)
            ->value('papel');

        if (!in_array($papel, ['admin', 'mestre']) && $personagem->getUsuarioId() !== $user->id) {
            return response()->json(['message' => 'Sem permissão para equipar item neste personagem'], 403);
        }

        if (
            $this->personagemRepository->equiparItem(
                $id,
                $mundoId,
                $request->input('item_id'),
                $request->input('quantidade', 1)
            )
        ) {
            return response()->json(['message' => 'Item equipado com sucesso']);
        }

        return response()->json(['message' => 'Erro ao equipar item'], 500);
    }
}
