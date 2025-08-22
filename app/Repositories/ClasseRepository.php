<?php

namespace App\Repositories;

use App\Domain\Classe\Classe;
use App\Domain\Classe\ClasseAtributo;
use App\Repositories\Interfaces\ClasseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ClasseRepository implements ClasseRepositoryInterface
{
    public function buscarAtributos(int $classeId): array
    {
        $atributos = DB::table('classes_atributos')
            ->where('classe_id', $classeId)
            ->get();

        return array_map(function ($dados) {
            $atributo = new ClasseAtributo(
                $dados->atributo_id,
                $dados->tipo_dado_id,
                $dados->base_fixa,
                $dados->limite_base_fixa,
                $dados->limite_tipo_dado_id,
                $dados->imutavel
            );
            $atributo->setId($dados->id);
            return $atributo;
        }, $atributos->all());
    }
    public function criar(Classe $classe): Classe
    {
        $id = DB::table('classes')->insertGetId([
            'mundo_id' => $classe->getMundoId(),
            'slug' => $classe->getSlug(),
            'nome' => $classe->getNome(),
            'descricao' => $classe->getDescricao()
        ]);

        $classe->setId($id);
        return $classe;
    }

    public function buscarPorId(int $id, int $mundoId): ?Classe
    {
        $dados = DB::table('classes')
            ->where('id', $id)
            ->where('mundo_id', $mundoId)
            ->first();

        if (!$dados) {
            return null;
        }

        $classe = $this->mapearParaDominio($dados);
        $this->carregarAtributos($classe);

        return $classe;
    }

    public function buscarPorSlug(string $slug, int $mundoId): ?Classe
    {
        $dados = DB::table('classes')
            ->where('slug', $slug)
            ->where('mundo_id', $mundoId)
            ->first();

        if (!$dados) {
            return null;
        }

        $classe = $this->mapearParaDominio($dados);
        $this->carregarAtributos($classe);

        return $classe;
    }

    public function listarPorMundo(int $mundoId): array
    {
        $classes = DB::table('classes')
            ->where('mundo_id', $mundoId)
            ->get();

        return array_map(function ($dados) {
            $classe = $this->mapearParaDominio($dados);
            $this->carregarAtributos($classe);
            return $classe;
        }, $classes->all());
    }

    public function atualizar(Classe $classe): bool
    {
        return DB::table('classes')
            ->where('id', $classe->getId())
            ->where('mundo_id', $classe->getMundoId())
            ->update([
                'nome' => $classe->getNome(),
                'descricao' => $classe->getDescricao()
            ]) > 0;
    }

    public function excluir(int $id, int $mundoId): bool
    {
        if ($this->possuiPersonagens($id)) {
            return false;
        }

        return DB::transaction(function () use ($id, $mundoId) {
            // Excluir atributos da classe
            DB::table('classes_atributos')
                ->where('classe_id', $id)
                ->delete();

            // Excluir a classe
            return DB::table('classes')
                ->where('id', $id)
                ->where('mundo_id', $mundoId)
                ->delete() > 0;
        });
    }

    public function possuiPersonagens(int $id): bool
    {
        return DB::table('personagens')
            ->where('classe_id', $id)
            ->exists();
    }

    public function vincularAtributos(int $classeId, array $atributos): array
    {
        $dados = array_map(function (ClasseAtributo $atributo) use ($classeId) {
            return [
                'classe_id' => $classeId,
                'atributo_id' => $atributo->getAtributoId(),
                'tipo_dado_id' => $atributo->getTipoDadoId(),
                'base_fixa' => $atributo->getBaseFixa(),
                'limite_base_fixa' => $atributo->getLimiteBaseFixa(),
                'limite_tipo_dado_id' => $atributo->getLimiteTipoDadoId(),
                'imutavel' => $atributo->isImutavel()
            ];
        }, $atributos);

        DB::table('classes_atributos')->insert($dados);

        return $dados;
    }

    public function atualizarAtributos(int $classeId, array $atributos): void
    {
        DB::transaction(function () use ($classeId, $atributos) {
            // Excluir atributos existentes
            DB::table('classes_atributos')
                ->where('classe_id', $classeId)
                ->delete();

            // Inserir novos atributos
            $this->vincularAtributos($classeId, $atributos);
        });
    }

    private function mapearParaDominio($dados): Classe
    {
        $classe = new Classe(
            $dados->mundo_id,
            $dados->slug,
            $dados->nome,
            $dados->descricao
        );

        $classe->setId($dados->id);
        return $classe;
    }

    private function carregarAtributos(Classe $classe): void
    {
        $atributos = DB::table('classes_atributos')
            ->where('classe_id', $classe->getId())
            ->get();

        $atributosObjetos = array_map(function ($dados) {
            $atributo = new ClasseAtributo(
                $dados->atributo_id,
                $dados->tipo_dado_id,
                $dados->base_fixa,
                $dados->limite_base_fixa,
                $dados->limite_tipo_dado_id,
                $dados->imutavel
            );
            $atributo->setId($dados->id);
            return $atributo;
        }, $atributos->all());

        $classe->setAtributos($atributosObjetos);
    }
}
