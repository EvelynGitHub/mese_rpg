<?php

namespace App\Repositories;

use App\Domain\Origem\Origem;
use App\Domain\Origem\OrigemEfeito;
use App\Domain\Origem\OrigemHabilidades;
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

        // $this->vincularHabilidades($id, $origem->getHabilidades());
        // $this->vincularEfeitos($id, $origem->getEfeitos());

        return $origem;
    }

    public function vincularHabilidades(int $origemId, array $habilidades): array
    {
        $dados = array_map(function (OrigemHabilidades $habilidade) use ($origemId) {
            return [
                'origem_id' => $origemId,
                'habilidade_id' => $habilidade->getHabilidadeId(),
            ];
        }, $habilidades);

        DB::table('origens_habilidades')->insert($dados);

        return $dados;
    }

    public function vincularEfeitos(int $origemId, array $efeitos): array
    {
        $dados = array_map(function (OrigemEfeito $efeito) use ($origemId) {
            return [
                'origem_id' => $origemId,
                'tipo' => $efeito->getTipo(),
                'atributo_id' => $efeito->getAtributoId(),
                'delta' => $efeito->getDelta(),
                'notas' => $efeito->getNotas() ? json_encode($efeito->getNotas()) : null,
                // 'criado_em' => now()
            ];
        }, $efeitos);

        DB::table('origens_efeitos')->insert($dados);

        return $dados;
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
        $habilidades = $this->buscarHabilidadesDaOrigem($id);

        $novaOrigem = new Origem(
            $origem->mundo_id,
            $origem->slug,
            $origem->nome,
            $origem->descricao
        );
        $novaOrigem->setId($origem->id);
        $novaOrigem->setEfeitos($efeitos);
        $novaOrigem->setHabilidades($habilidades);

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
            $this->carregarHabilidades($novaOrigem);
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
                'descricao' => $origem->getDescricao()
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

    private function buscarHabilidadesDaOrigem(int $origemId): array
    {
        return DB::table('origens_habilidades')
            ->where('origem_id', $origemId)
            ->get()
            ->map(function ($habilidade) {
                $novaHabilidade = new OrigemHabilidades(
                    $habilidade->origem_id,
                    $habilidade->habilidade_id,
                    null
                );
                $novaHabilidade->setId($habilidade->id);
                return $novaHabilidade;
            })->all();
    }

    // public function vincularEfeitos(int $origemId, array $efeitos): void
    // {
    //     DB::transaction(function () use ($origemId, $efeitos) {
    //         foreach ($efeitos as $efeito) {
    //             DB::table('origens_efeitos')->insert([
    //                 'origem_id' => $origemId,
    //                 'tipo' => $efeito->getTipo(),
    //                 'atributo_id' => $efeito->getAtributoId(),
    //                 'delta' => $efeito->getDelta(),
    //                 'notas' => $efeito->getNotas() ? json_encode($efeito->getNotas()) : null,
    //                 'criado_em' => now()
    //             ]);
    //         }
    //     });
    // }

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
                    'notas' => $efeito->getNotas() ? json_encode($efeito->getNotas()) : null
                ]);
            }
        });
    }

    public function atualizarHabilidades(int $origemId, array $habilidades): void
    {
        DB::transaction(function () use ($origemId, $habilidades) {
            // Remove todos os efeitos existentes
            DB::table('origens_habilidades')
                ->where('origem_id', $origemId)
                ->delete();

            // Insere os novos efeitos
            foreach ($habilidades as $efeito) {
                DB::table('origens_habilidades')->insert([
                    'origem_id' => $origemId,
                    'habilidade_id' => $efeito->getHabilidadeId()
                ]);
            }
        });
    }

    private function carregarHabilidades(Origem $origem): void
    {
        $habilidadesOrigem = DB::table('origens_habilidades')
            ->join('habilidades', 'origens_habilidades.habilidade_id', '=', 'habilidades.id')
            ->where('origem_id', $origem->getId())
            ->get(['habilidades.nome', 'origens_habilidades.*']);

        $habilidades = array_map(function ($dados) use ($origem) {
            // $habilidade = new origemHabilidades(
            //     $origem->getId(),
            //     $dados->habilidade_id,
            //     null
            // );
            // $habilidade->setId($dados->id);
            // $origem->adicionarHabilidade($habilidade);
            return [
                'id' => $dados->id,
                'origem_id' => $dados->origem_id,
                'habilidade_id' => $dados->habilidade_id,
                'nome' => $dados->nome
            ];
        }, $habilidadesOrigem->all());

        $origem->setHabilidades($habilidades);
    }
}
