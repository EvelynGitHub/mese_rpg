<?php

namespace App\Repositories;

use App\Domain\Atributo\Atributo;
use App\Repositories\Interfaces\AtributoRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AtributoRepository implements AtributoRepositoryInterface
{
    public function criar(Atributo $atributo): Atributo
    {
        $id = DB::table('atributos')->insertGetId([
            'mundo_id' => $atributo->getMundoId(),
            'chave' => $atributo->getChave(),
            'nome' => $atributo->getNome(),
            'descricao' => $atributo->getDescricao(),
            'exibir' => $atributo->isExibir()
        ]);

        $atributo->setId($id);
        return $atributo;
    }

    public function buscarPorId(int $id, int $mundoId): ?Atributo
    {
        $dados = DB::table('atributos')
            ->where('id', $id)
            ->where('mundo_id', $mundoId)
            ->first();

        if (!$dados) {
            return null;
        }

        return $this->mapearParaDominio($dados);
    }

    public function buscarPorChave(string $chave, int $mundoId): ?Atributo
    {
        $dados = DB::table('atributos')
            ->where('chave', $chave)
            ->where('mundo_id', $mundoId)
            ->first();

        if (!$dados) {
            return null;
        }

        return $this->mapearParaDominio($dados);
    }

    public function listarPorMundo(int $mundoId): array
    {
        $atributos = DB::table('atributos')
            ->where('mundo_id', $mundoId)
            ->get();

        return array_map(
            fn($dados) => $this->mapearParaDominio($dados),
            $atributos->all()
        );
    }

    public function atualizar(Atributo $atributo): bool
    {
        return DB::table('atributos')
            ->where('id', $atributo->getId())
            ->where('mundo_id', $atributo->getMundoId())
            ->update([
                'chave' => $atributo->getChave(),
                'nome' => $atributo->getNome(),
                'descricao' => $atributo->getDescricao(),
                'exibir' => $atributo->isExibir()
            ]) > 0;
    }

    public function excluir(int $id, int $mundoId): bool
    {
        if ($this->possuiDependencias($id)) {
            return false;
        }

        return DB::table('atributos')
            ->where('id', $id)
            ->where('mundo_id', $mundoId)
            ->delete() > 0;
    }

    public function possuiDependencias(int $id): bool
    {
        // Verifica se existe em classes_atributos
        $temClasseAtributo = DB::table('classes_atributos')
            ->where('atributo_id', $id)
            ->exists();

        if ($temClasseAtributo) {
            return true;
        }

        // Verifica se existe em origens_efeitos
        $temOrigemEfeito = DB::table('origens_efeitos')
            ->where('atributo_id', $id)
            ->exists();

        return $temOrigemEfeito;
    }

    private function mapearParaDominio($dados): Atributo
    {
        $atributo = new Atributo(
            $dados->mundo_id,
            $dados->chave,
            $dados->nome,
            $dados->descricao,
            $dados->exibir
        );

        $atributo->setId($dados->id);
        return $atributo;
    }
}
