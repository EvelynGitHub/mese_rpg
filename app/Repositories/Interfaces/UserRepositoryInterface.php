<?php

namespace App\Repositories\Interfaces;

use App\Domain\User\User;

interface UserRepositoryInterface
{
    public function create(User $user): User;
    public function update(User $user): User;
    public function delete(int $id): void;
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function getPapeisPorMundo(int $usuarioId): array;
    public function getUsuariosMundo(int $mundoId): array;
    public function addUsuarioMundo(int $userId, int $mundoId, string $papel): void;
    public function removeUsuarioMundo(int $userId, int $mundoId): void;
    public function getPapelUsuarioMundo(int $userId, int $mundoId): ?string;
}
