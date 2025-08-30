<?php

namespace App\Repositories;

use App\Domain\User\User;
use App\Domain\User\Exception\UserNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create(User $user): User
    {
        $id = DB::table('usuarios')->insertGetId([
            'nome' => $user->getNome(),
            'email' => $user->getEmail(),
            'senha_hash' => $user->getSenha(),
            'criado_em' => now(),
            'atualizado_em' => now()
        ]);

        $user->setId($id);
        return $user;
    }

    public function update(User $user): User
    {
        $updated = DB::table('usuarios')
            ->where('id', $user->getId())
            ->update([
                'nome' => $user->getNome(),
                'email' => $user->getEmail(),
                'atualizado_em' => now()
            ]);

        if (!$updated) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function delete(int $id): void
    {
        $deleted = DB::table('usuarios')->where('id', $id)->delete();

        if (!$deleted) {
            throw new UserNotFoundException();
        }
    }

    public function findById(int $id): ?User
    {
        $data = DB::table('usuarios')->where('id', $id)->first();

        if (!$data) {
            return null;
        }

        return $this->mapToUser($data);
    }

    public function findByEmail(string $email): ?User
    {
        $data = DB::table('usuarios')->where('email', $email)->first();

        if (!$data) {
            return null;
        }

        return $this->mapToUser($data);
    }

    public function getPapeisPorMundo(int $usuarioId): array
    {
        // Consulta a tabela usuarios_mundos para buscar os papéis
        $papeis = DB::table('usuarios_mundos')
            ->where('usuario_id', $usuarioId)
            ->whereIn('papel', ['admin', 'mestre'])
            ->get();

        $resultado = [];
        foreach ($papeis as $item) {
            // Mapeia o mundo_id para o papel correspondente
            $resultado[$item->mundo_id] = $item->papel;
        }

        return $resultado;
    }

    public function getUsuariosMundo(int $mundoId): array
    {
        return DB::table('usuarios_mundos')
            ->join('usuarios', 'usuarios.id', '=', 'usuarios_mundos.usuario_id')
            ->where('mundo_id', $mundoId)
            ->select([
                'usuarios.id',
                'usuarios.nome',
                'usuarios.email',
                'usuarios_mundos.papel'
            ])
            ->get()
            ->toArray();
    }

    public function addUsuarioMundo(int $userId, int $mundoId, string $papel): void
    {
        DB::table('usuarios_mundos')->insert([
            'usuario_id' => $userId,
            'mundo_id' => $mundoId,
            'papel' => $papel
        ]);
    }

    public function removeUsuarioMundo(int $userId, int $mundoId): void
    {
        DB::table('usuarios_mundos')
            ->where('usuario_id', $userId)
            ->where('mundo_id', $mundoId)
            ->delete();
    }

    public function getPapelUsuarioMundo(int $userId, int $mundoId): ?string
    {
        $result = DB::table('usuarios_mundos')
            ->where('usuario_id', $userId)
            ->where('mundo_id', $mundoId)
            ->value('papel');

        return $result ?? null;
    }

    private function mapToUser($data): User
    {
        $user = new User(
            $data->nome,
            $data->email,
            ""
        );
        $user->setId($data->id);
        $user->setSenhaHash($data->senha_hash ?? null);
        return $user;
    }
}
