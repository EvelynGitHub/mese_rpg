<?php

namespace App\Repositories\Interfaces;

use App\Domain\Campanha\Campanha;

interface CampanhaRepositoryInterface
{
    public function criar(Campanha $campanha): Campanha;
    public function atualizar(Campanha $campanha): void;
    public function excluir(int $id): void;
    public function buscarPorId(int $id): ?Campanha;
    public function listarPorMundo(int $mundoId): array;

}
