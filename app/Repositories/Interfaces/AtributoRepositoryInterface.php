<?php

namespace App\Repositories\Interfaces;

use App\Domain\Atributo\Atributo;

interface AtributoRepositoryInterface
{
    public function criar(Atributo $atributo): Atributo;
    public function buscarPorId(int $id, int $mundoId): ?Atributo;
    public function buscarPorIds(array $ids, int $mundoId): array;
    public function buscarPorChave(string $chave, int $mundoId): ?Atributo;
    public function listarPorMundo(int $mundoId, int $offset = 0): array;
    public function atualizar(Atributo $atributo): bool;
    public function excluir(int $id, int $mundoId): bool;
    public function possuiDependencias(int $id): bool;
    public function obterTiposDados(): array;
}
