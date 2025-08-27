<?php

namespace App\Repositories\Interfaces;

use App\Domain\Mundo\Mundo;
use App\Domain\Mundo\MundoRegras;

interface MundoRepositoryInterface
{
    public function create(Mundo $mundo): Mundo;
    public function update(Mundo $mundo): void;
    public function delete(int $id): void;
    public function findById(int $id): ?Mundo;
    /**
     * Summary of findAllByUserId
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return Mundo[]
     */
    public function findAllByUserId(int $userId, int $limit = 10, int $offset = 0): array;
    public function addMember(int $mundoId, int $userId, string $role): void;
    public function removeMember(int $mundoId, int $userId): void;
    public function getMemberRole(int $mundoId, int $userId): ?string;
    public function countAdmins(int $mundoId): int;
    public function createRules(MundoRegras $rules): MundoRegras;
    public function updateRules(MundoRegras $rules): void;
    public function getRules(int $mundoId): ?MundoRegras;
}
