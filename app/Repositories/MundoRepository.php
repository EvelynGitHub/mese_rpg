<?php

namespace App\Repositories;

use App\Domain\Mundo\Mundo;
use App\Domain\Mundo\MundoRegras;
use App\Repositories\Interfaces\MundoRepositoryInterface;
use Illuminate\Support\Facades\DB;

class MundoRepository implements MundoRepositoryInterface
{
    public function create(Mundo $mundo): Mundo
    {
        $id = DB::table('mundos')->insertGetId([
            'nome' => $mundo->getNome(),
            'descricao' => $mundo->getDescricao(),
            'criado_por' => $mundo->getCriadoPor(),
            'criado_em' => $mundo->getCriadoEm()
        ]);

        $this->addMember($id, $mundo->getCriadoPor(), "admin");

        return $this->findById($id);
    }

    public function findById(int $id): ?Mundo
    {
        $mundo = DB::table('mundos')
            ->where('id', $id)
            ->first();

        if (!$mundo) {
            return null;
        }

        return $this->mapearParaDominio($mundo);
    }

    public function findAllByUserId(int $userId, int $limit = 10, int $offset = 0): array
    {
        $mundos = DB::table('mundos')
            ->join('usuarios_mundos', 'mundos.id', '=', 'usuarios_mundos.mundo_id')
            ->where('usuarios_mundos.usuario_id', $userId)
            ->limit($limit)->offset($offset)
            ->select('mundos.*')
            ->get();

        return array_map(
            fn($mundo) => $this->mapearParaDominio($mundo),
            $mundos->all()
        );
    }

    public function update(Mundo $mundo): void
    {
        DB::table('mundos')
            ->where('id', $mundo->getId())
            ->update([
                'nome' => $mundo->getNome(),
                'descricao' => $mundo->getDescricao()
            ]);
    }

    public function delete(int $id): void
    {
        DB::table('mundos')
            ->where('id', $id)
            ->delete();
    }

    private function mapearParaDominio($dados): Mundo
    {
        $mundo = new Mundo(
            $dados->nome,
            $dados->descricao,
            $dados->criado_por
        );

        // Reflection para setar o ID, já que é privado
        $reflection = new \ReflectionClass($mundo);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($mundo, $dados->id);

        return $mundo;
    }

    public function addMember(int $mundoId, int $userId, string $role): void
    {
        DB::table('usuarios_mundos')->insert([
            'mundo_id' => $mundoId,
            'usuario_id' => $userId,
            'papel' => $role
        ]);
    }

    public function removeMember(int $mundoId, int $userId): void
    {
        DB::table('usuarios_mundos')
            ->where('mundo_id', $mundoId)
            ->where('usuario_id', $userId)
            ->delete();
    }

    public function getMemberRole(int $mundoId, int $userId): ?string
    {
        $member = DB::table('usuarios_mundos')
            ->where('mundo_id', $mundoId)
            ->where('usuario_id', $userId)
            ->first();

        return $member ? $member->papel : null;
    }

    public function countAdmins(int $mundoId): int
    {
        return DB::table('usuarios_mundos')
            ->where('mundo_id', $mundoId)
            ->where('papel', 'admin')
            ->count();
    }

    public function createRules(MundoRegras $rules): MundoRegras
    {
        $id = DB::table('mundos_regras')->insertGetId([
            'mundo_id' => $rules->getMundoId(),
            'pontos_base_por_personagem' => $rules->getPontosBasePorPersonagem(),
            'niveis_dado_por_personagem' => $rules->getNiveisDadoPorPersonagem(),
            'sequencia_dados' => json_encode($rules->getSequenciaDados()),
            'limite_max_tipo_dado_id' => $rules->getLimiteMaxTipoDadoId(),
            'permite_pvp' => $rules->getPermitePvp(),
            'permite_pve' => $rules->getPermitePve(),
            'criado_em' => now()
        ]);

        $rules->setId($id);
        return $rules;
    }

    public function updateRules(MundoRegras $rules): void
    {
        DB::table('mundos_regras')
            ->where('mundo_id', $rules->getMundoId())
            ->update([
                'pontos_base_por_personagem' => $rules->getPontosBasePorPersonagem(),
                'niveis_dado_por_personagem' => $rules->getNiveisDadoPorPersonagem(),
                'sequencia_dados' => json_encode($rules->getSequenciaDados()),
                'limite_max_tipo_dado_id' => $rules->getLimiteMaxTipoDadoId(),
                'permite_pvp' => $rules->getPermitePvp(),
                'permite_pve' => $rules->getPermitePve(),
                'atualizado_em' => now()
            ]);
    }

    public function getRules(int $mundoId): ?MundoRegras
    {
        $rules = DB::table('mundos_regras')
            ->where('mundo_id', $mundoId)
            ->first();

        if (!$rules) {
            return null;
        }

        $mundoRegras = new MundoRegras(
            $mundoId,
            $rules->pontos_base_por_personagem,
            $rules->niveis_dado_por_personagem,
            json_decode($rules->sequencia_dados, true),
            $rules->limite_max_tipo_dado_id,
            (bool) $rules->permite_pvp,
            (bool) $rules->permite_pve
        );

        $mundoRegras->setId($rules->id);
        return $mundoRegras;
    }
}
