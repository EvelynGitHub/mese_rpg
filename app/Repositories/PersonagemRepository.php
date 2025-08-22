<?php

namespace App\Repositories;

use App\Domain\Personagem\Personagem;
use App\Repositories\Interfaces\PersonagemRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PersonagemRepository implements PersonagemRepositoryInterface
{
    public function criar(Personagem $personagem): Personagem
    {
        $id = DB::transaction(function () use ($personagem) {
            $id = DB::table('personagens')->insertGetId([
                'mundo_id' => $personagem->getMundoId(),
                'usuario_id' => $personagem->getUsuarioId(),
                'campanha_id' => $personagem->getCampanhaId(),
                'classe_id' => $personagem->getClasseId(),
                'origem_id' => $personagem->getOrigemId(),
                'nome' => $personagem->getNome(),
                'pontos_base' => $personagem->getPontosBase(),
                'pontos_base_map' => json_encode($personagem->getPontosBaseMap()),
                'niveis_dado' => json_encode($personagem->getNiveisDado()),
                'atributos_override' => json_encode($personagem->getAtributosOverride()),
                'inventario' => json_encode($personagem->getInventario()),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Registra auditoria
            DB::table('auditoria')->insert([
                'evento' => 'CRIAR_PERSONAGEM',
                'usuario_id' => auth()->id(),
                'mundo_id' => $personagem->getMundoId(),
                'payload_before' => null,
                'payload_after' => json_encode(['personagem_id' => $id] + [
                    'mundo_id' => $personagem->getMundoId(),
                    'usuario_id' => $personagem->getUsuarioId(),
                    'campanha_id' => $personagem->getCampanhaId(),
                    'classe_id' => $personagem->getClasseId(),
                    'origem_id' => $personagem->getOrigemId(),
                    'nome' => $personagem->getNome(),
                    'pontos_base' => $personagem->getPontosBase(),
                    'pontos_base_map' => $personagem->getPontosBaseMap(),
                    'niveis_dado' => $personagem->getNiveisDado(),
                    'atributos_override' => $personagem->getAtributosOverride(),
                    'inventario' => $personagem->getInventario()
                ]),
                'criado_em' => now()
            ]);

            return $id;
        });

        $personagem->setId($id);
        return $personagem;
    }

    public function buscarPorId(int $id, int $mundoId): ?Personagem
    {
        $personagemData = DB::table('personagens')
            ->where('id', $id)
            ->where('mundo_id', $mundoId)
            ->first();

        if (!$personagemData) {
            return null;
        }

        return $this->mapToEntity($personagemData);
    }

    public function listarPorMundo(int $mundoId, int $usuarioId = null): array
    {
        $query = DB::table('personagens')
            ->where('mundo_id', $mundoId);

        if ($usuarioId !== null) {
            $query->where('usuario_id', $usuarioId);
        }

        return $query->get()
            ->map(fn($p) => $this->mapToEntity($p))
            ->all();
    }

    public function atualizar(Personagem $personagem): bool
    {
        return DB::transaction(function () use ($personagem) {
            $antigaData = DB::table('personagens')
                ->where('id', $personagem->getId())
                ->where('mundo_id', $personagem->getMundoId())
                ->first();

            $atualizado = DB::table('personagens')
                ->where('id', $personagem->getId())
                ->where('mundo_id', $personagem->getMundoId())
                ->update([
                    'campanha_id' => $personagem->getCampanhaId(),
                    'nome' => $personagem->getNome(),
                    'pontos_base' => $personagem->getPontosBase(),
                    'pontos_base_map' => json_encode($personagem->getPontosBaseMap()),
                    'niveis_dado' => json_encode($personagem->getNiveisDado()),
                    'atributos_override' => json_encode($personagem->getAtributosOverride()),
                    'inventario' => json_encode($personagem->getInventario()),
                    'updated_at' => now()
                ]);

            if ($atualizado) {
                // Registra auditoria
                DB::table('auditoria')->insert([
                    'evento' => 'ATUALIZAR_PERSONAGEM',
                    'usuario_id' => auth()->id(),
                    'mundo_id' => $personagem->getMundoId(),
                    'payload_before' => json_encode($antigaData),
                    'payload_after' => json_encode([
                        'id' => $personagem->getId(),
                        'campanha_id' => $personagem->getCampanhaId(),
                        'nome' => $personagem->getNome(),
                        'pontos_base' => $personagem->getPontosBase(),
                        'pontos_base_map' => $personagem->getPontosBaseMap(),
                        'niveis_dado' => $personagem->getNiveisDado(),
                        'atributos_override' => $personagem->getAtributosOverride(),
                        'inventario' => $personagem->getInventario()
                    ]),
                    'criado_em' => now()
                ]);
            }

            return $atualizado > 0;
        });
    }

    public function resetarAlocacao(int $id, int $mundoId): bool
    {
        return DB::transaction(function () use ($id, $mundoId) {
            $antigaData = DB::table('personagens')
                ->where('id', $id)
                ->where('mundo_id', $mundoId)
                ->first();

            $atualizado = DB::table('personagens')
                ->where('id', $id)
                ->where('mundo_id', $mundoId)
                ->update([
                    'pontos_base_map' => '{}',
                    'niveis_dado' => '{}',
                    'updated_at' => now()
                ]);

            if ($atualizado) {
                DB::table('auditoria')->insert([
                    'evento' => 'RESETAR_ALOCACAO_PERSONAGEM',
                    'usuario_id' => auth()->id(),
                    'mundo_id' => $mundoId,
                    'payload_before' => json_encode($antigaData),
                    'payload_after' => json_encode(['id' => $id]),
                    'criado_em' => now()
                ]);
            }

            return $atualizado > 0;
        });
    }

    public function equiparItem(int $id, int $mundoId, int $itemId, int $quantidade = 1): bool
    {
        return DB::transaction(function () use ($id, $mundoId, $itemId, $quantidade) {
            $personagem = DB::table('personagens')
                ->where('id', $id)
                ->where('mundo_id', $mundoId)
                ->first();

            if (!$personagem) {
                return false;
            }

            $inventario = json_decode($personagem->inventario, true) ?? [];
            $inventario['itens'][$itemId] = ($inventario['itens'][$itemId] ?? 0) + $quantidade;

            $atualizado = DB::table('personagens')
                ->where('id', $id)
                ->where('mundo_id', $mundoId)
                ->update([
                    'inventario' => json_encode($inventario),
                    'updated_at' => now()
                ]);

            if ($atualizado) {
                DB::table('auditoria')->insert([
                    'evento' => 'EQUIPAR_ITEM_PERSONAGEM',
                    'usuario_id' => auth()->id(),
                    'mundo_id' => $mundoId,
                    'payload_before' => json_encode(['inventario' => $personagem->inventario]),
                    'payload_after' => json_encode([
                        'id' => $id,
                        'item_id' => $itemId,
                        'quantidade' => $quantidade,
                        'inventario' => $inventario
                    ]),
                    'criado_em' => now()
                ]);
            }

            return $atualizado > 0;
        });
    }

    private function mapToEntity($data): Personagem
    {
        $personagem = new Personagem(
            $data->mundo_id,
            $data->usuario_id,
            $data->classe_id,
            $data->nome,
            json_decode($data->pontos_base_map, true) ?? [],
            json_decode($data->niveis_dado, true) ?? [],
            $data->campanha_id,
            $data->origem_id,
            json_decode($data->atributos_override, true),
            json_decode($data->inventario, true) ?? []
        );
        $personagem->setId($data->id);
        return $personagem;
    }
}
