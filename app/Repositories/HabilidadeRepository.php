<?php

namespace App\Repositories;

use App\Domain\Habilidade\Habilidade;
use App\Repositories\Interfaces\HabilidadeRepositoryInterface;
use Illuminate\Support\Facades\DB;

class HabilidadeRepository implements HabilidadeRepositoryInterface
{
    public function criar(Habilidade $habilidade): Habilidade
    {
        $id = DB::table('habilidades')->insertGetId([
            'mundo_id' => $habilidade->getMundoId(),
            'slug' => $habilidade->getSlug(),
            'nome' => $habilidade->getNome(),
            'descricao' => $habilidade->getDescricao(),
            'bonus' => json_encode($habilidade->getBonus()),
            'ativa' => $habilidade->isAtiva(),
            // 'criado_em' => date("Y-m-d H:i:s")
        ]);

        $habilidade->setId($id);
        return $habilidade;
    }

    public function buscarPorId(int $id, int $mundoId): ?Habilidade
    {
        $registro = DB::table('habilidades')
            ->where('id', $id)
            ->where('mundo_id', $mundoId)
            ->first();

        if (!$registro) {
            return null;
        }

        $habilidade = new Habilidade(
            $registro->mundo_id,
            $registro->slug,
            $registro->nome,
            $registro->descricao,
            json_decode($registro->bonus, true),
            $registro->ativa
        );
        $habilidade->setId($registro->id);

        return $habilidade;
    }

    public function buscarPorSlug(string $slug, int $mundoId): ?Habilidade
    {
        $registro = DB::table('habilidades')
            ->where('slug', $slug)
            ->where('mundo_id', $mundoId)
            ->first();

        if (!$registro) {
            return null;
        }

        $habilidade = new Habilidade(
            $registro->mundo_id,
            $registro->slug,
            $registro->nome,
            $registro->descricao,
            json_decode($registro->bonus, true),
            $registro->ativa
        );
        $habilidade->setId($registro->id);

        return $habilidade;
    }

    public function listarPorMundo(int $mundoId, int $offset = 0): array
    {
        $registros = DB::table('habilidades')
            ->where('mundo_id', $mundoId)
            ->orderBy('id', "desc")
            ->offset($offset)
            ->limit(10)
            ->get();

        $habilidades = [];
        foreach ($registros as $registro) {
            $habilidade = new Habilidade(
                $registro->mundo_id,
                $registro->slug,
                $registro->nome,
                $registro->descricao,
                json_decode($registro->bonus, true),
                $registro->ativa
            );
            $habilidade->setId($registro->id);
            $habilidades[] = $habilidade;
        }

        return $habilidades;
    }

    public function atualizar(Habilidade $habilidade): bool
    {
        return DB::table('habilidades')
            ->where('id', $habilidade->getId())
            ->where('mundo_id', $habilidade->getMundoId())
            ->update([
                'slug' => $habilidade->getSlug(),
                'nome' => $habilidade->getNome(),
                'descricao' => $habilidade->getDescricao(),
                'bonus' => json_encode($habilidade->getBonus()),
                'ativa' => $habilidade->isAtiva(),
                // 'atualizado_em' => now()
            ]) > 0;
    }

    public function excluir(int $id, int $mundoId): bool
    {
        return DB::table('habilidades')
            ->where('id', $id)
            ->where('mundo_id', $mundoId)
            ->delete() > 0;
    }

    public function vincularClasse(int $habilidadeId, int $classeId): bool
    {
        // Verifica se já existe o vínculo
        $existente = DB::table('classes_habilidades')
            ->where('habilidade_id', $habilidadeId)
            ->where('classe_id', $classeId)
            ->exists();

        if ($existente) {
            return false;
        }

        DB::table('classes_habilidades')->insert([
            'habilidade_id' => $habilidadeId,
            'classe_id' => $classeId,
            'criado_em' => now()
        ]);

        return true;
    }

    public function vincularOrigem(int $habilidadeId, int $origemId): bool
    {
        // Verifica se já existe o vínculo
        $existente = DB::table('origens_habilidades')
            ->where('habilidade_id', $habilidadeId)
            ->where('origem_id', $origemId)
            ->exists();

        if ($existente) {
            return false;
        }

        DB::table('origens_habilidades')->insert([
            'habilidade_id' => $habilidadeId,
            'origem_id' => $origemId,
            'criado_em' => now()
        ]);

        return true;
    }

    public function desvincularClasse(int $habilidadeId, int $classeId): bool
    {
        return DB::table('classes_habilidades')
            ->where('habilidade_id', $habilidadeId)
            ->where('classe_id', $classeId)
            ->delete() > 0;
    }

    public function desvincularOrigem(int $habilidadeId, int $origemId): bool
    {
        return DB::table('origens_habilidades')
            ->where('habilidade_id', $habilidadeId)
            ->where('origem_id', $origemId)
            ->delete() > 0;
    }

    public function listarPorClasse(int $classeId): array
    {
        $registros = DB::table('habilidades')
            ->join('classes_habilidades', 'habilidades.id', '=', 'classes_habilidades.habilidade_id')
            ->where('classes_habilidades.classe_id', $classeId)
            ->select('habilidades.*')
            ->orderBy('habilidades.nome')
            ->get();

        $habilidades = [];
        foreach ($registros as $registro) {
            $habilidade = new Habilidade(
                $registro->mundo_id,
                $registro->slug,
                $registro->nome,
                $registro->descricao,
                json_decode($registro->bonus, true),
                $registro->ativa
            );
            $habilidade->setId($registro->id);
            $habilidades[] = $habilidade;
        }

        return $habilidades;
    }

    public function listarPorOrigem(int $origemId): array
    {
        $registros = DB::table('habilidades')
            ->join('origens_habilidades', 'habilidades.id', '=', 'origens_habilidades.habilidade_id')
            ->where('origens_habilidades.origem_id', $origemId)
            ->select('habilidades.*')
            ->orderBy('habilidades.nome')
            ->get();

        $habilidades = [];
        foreach ($registros as $registro) {
            $habilidade = new Habilidade(
                $registro->mundo_id,
                $registro->slug,
                $registro->nome,
                $registro->descricao,
                json_decode($registro->bonus, true),
                $registro->ativa
            );
            $habilidade->setId($registro->id);
            $habilidades[] = $habilidade;
        }

        return $habilidades;
    }
}
