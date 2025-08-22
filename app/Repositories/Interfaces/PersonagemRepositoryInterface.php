<?php

namespace App\Repositories\Interfaces;

use App\Domain\Personagem\Personagem;

interface PersonagemRepositoryInterface
{
    public function criar(Personagem $personagem): Personagem;

    public function buscarPorId(int $id, int $mundoId): ?Personagem;

    public function listarPorMundo(int $mundoId, int $usuarioId = null): array;

    public function atualizar(Personagem $personagem): bool;

    public function resetarAlocacao(int $id, int $mundoId): bool;

    public function equiparItem(int $id, int $mundoId, int $itemId, int $quantidade = 1): bool;
}
