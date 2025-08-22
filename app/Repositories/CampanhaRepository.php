<?php

namespace App\Repositories;

use App\Domain\Campanha\Campanha;
use App\Repositories\Interfaces\CampanhaRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CampanhaRepository implements CampanhaRepositoryInterface
{
    public function criar(Campanha $campanha): Campanha
    {
        $id = DB::table('campanhas')->insertGetId([
            'mundo_id' => $campanha->getMundoId(),
            'nome' => $campanha->getNome(),
            'descricao' => $campanha->getDescricao(),
            'data_inicio' => $campanha->getDataInicio(),
            'data_fim' => $campanha->getDataFim(),
            'criado_por' => $campanha->getCriadoPor(),
            'criado_em' => $campanha->getCriadoEm()
        ]);

        $campanha->setId($id);
        return $campanha;
    }

    public function atualizar(Campanha $campanha): void
    {
        DB::table('campanhas')
            ->where('id', $campanha->getId())
            ->where('mundo_id', $campanha->getMundoId())
            ->update([
                'nome' => $campanha->getNome(),
                'descricao' => $campanha->getDescricao(),
                'data_inicio' => $campanha->getDataInicio(),
                'data_fim' => $campanha->getDataFim()
            ]);
    }

    public function excluir(int $id): void
    {
        DB::table('campanhas')
            ->where('id', $id)
            ->delete();
    }

    public function buscarPorId(int $id): ?Campanha
    {
        $campanha = DB::table('campanhas')
            ->where('id', $id)
            ->first();

        if (!$campanha) {
            return null;
        }

        return $this->mapearParaDominio($campanha);
    }

    public function listarPorMundo(int $mundoId): array
    {
        $campanhas = DB::table('campanhas')
            ->where('mundo_id', $mundoId)
            ->get();

        return array_map(
            fn($campanha) => $this->mapearParaDominio($campanha),
            $campanhas->all()
        );
    }

    private function mapearParaDominio($dados): Campanha
    {
        $campanha = new Campanha(
            $dados->mundo_id,
            $dados->nome,
            $dados->criado_por,
            $dados->descricao,
            $dados->data_inicio ? new \DateTimeImmutable($dados->data_inicio) : null,
            $dados->data_fim ? new \DateTimeImmutable($dados->data_fim) : null
        );

        $campanha->setId($dados->id);
        return $campanha;
    }


}
