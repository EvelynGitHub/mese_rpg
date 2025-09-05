<?php

namespace App\Repositories;

use App\Domain\Origem\Origem;
use App\Domain\Origem\OrigemEfeito;
use App\Repositories\Interfaces\OrigemRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrigemRepository implements OrigemRepositoryInterface
{
    public function criar(Origem $origem): Origem
    {
        $id = DB::table('origens')->insertGetId([
            'mundo_id' => $origem->getMundoId(),
            'slug' => $origem->getSlug(),
            'nome' => $origem->getNome(),
            'descricao' => $origem->getDescricao(),
            // 'criado_em' => now()
        ]);

        $origem->setId($id);
        return $origem;
    }

    public function buscarPorId(int $id, int $mundoId): ?Origem
    {
        $origem = DB::table('origens')
            ->where('id', $id)
            ->where('mundo_id', $mundoId)
            ->first();

        if (!$origem) {
            return null;
        }

        $efeitos = $this->buscarEfeitosDaOrigem($id);

        $novaOrigem = new Origem(
            $origem->mundo_id,
            $origem->slug,
            $origem->nome,
            $origem->descricao
        );
        $novaOrigem->setId($origem->id);
        $novaOrigem->setEfeitos($efeitos);

        return $novaOrigem;
    }

    public function buscarPorSlug(string $slug, int $mundoId): ?Origem
    {
        $origem = DB::table('origens')
            ->where('slug', $slug)
            ->where('mundo_id', $mundoId)
            ->first();

        if (!$origem) {
            return null;
        }

        $novaOrigem = new Origem(
            $origem->mundo_id,
            $origem->slug,
            $origem->nome,
            $origem->descricao
        );
        $novaOrigem->setId($origem->id);
        return $novaOrigem;
    }

    public function listarPorMundo(int $mundoId): array
    {
        $origens = DB::table('origens')
            ->where('mundo_id', $mundoId)
            ->get();

        return $origens->map(function ($origem) {
            $novaOrigem = new Origem(
                $origem->mundo_id,
                $origem->slug,
                $origem->nome,
                $origem->descricao
            );
            $novaOrigem->setId($origem->id);
            return $novaOrigem;
        })->all();
    }

    public function atualizar(Origem $origem): bool
    {
        return DB::table('origens')
            ->where('id', $origem->getId())
            ->where('mundo_id', $origem->getMundoId())
            ->update([
                'slug' => $origem->getSlug(),
                'nome' => $origem->getNome(),
                'descricao' => $origem->getDescricao(),
                'atualizado_em' => now()
            ]);
    }

    public function excluir(int $id, int $mundoId): bool
    {
        return DB::table('origens')
            ->where('id', $id)
            ->where('mundo_id', $mundoId)
            ->delete() > 0;
    }

    public function possuiPersonagens(int $id): bool
    {
        return DB::table('personagens')
            ->where('origem_id', $id)
            ->exists();
    }

    private function buscarEfeitosDaOrigem(int $origemId): array
    {
        return DB::table('origens_efeitos')
            ->where('origem_id', $origemId)
            ->get()
            ->map(function ($efeito) {
                $novoEfeito = new OrigemEfeito(
                    $efeito->tipo,
                    $efeito->atributo_id,
                    $efeito->delta,
                    $efeito->notas ? json_decode($efeito->notas, true) : null
                );
                $novoEfeito->setId($efeito->id);
                return $novoEfeito;
            })->all();
    }

    public function vincularEfeitos(int $origemId, array $efeitos): void
    {
        DB::transaction(function () use ($origemId, $efeitos) {
            foreach ($efeitos as $efeito) {
                DB::table('origens_efeitos')->insert([
                    'origem_id' => $origemId,
                    'tipo' => $efeito->getTipo(),
                    'atributo_id' => $efeito->getAtributoId(),
                    'delta' => $efeito->getDelta(),
                    'notas' => $efeito->getNotas() ? json_encode($efeito->getNotas()) : null,
                    'criado_em' => now()
                ]);
            }
        });
    }

    public function atualizarEfeitos(int $origemId, array $efeitos): void
    {
        DB::transaction(function () use ($origemId, $efeitos) {
            // Remove todos os efeitos existentes
            DB::table('origens_efeitos')
                ->where('origem_id', $origemId)
                ->delete();

            // Insere os novos efeitos
            foreach ($efeitos as $efeito) {
                DB::table('origens_efeitos')->insert([
                    'origem_id' => $origemId,
                    'tipo' => $efeito->getTipo(),
                    'atributo_id' => $efeito->getAtributoId(),
                    'delta' => $efeito->getDelta(),
                    'notas' => $efeito->getNotas() ? json_encode($efeito->getNotas()) : null,
                    'criado_em' => now()
                ]);
            }
        });
    }
}
