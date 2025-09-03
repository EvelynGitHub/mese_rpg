<?php

namespace App\Repositories\Interfaces;

use App\Domain\Classe\Classe;

interface ClasseRepositoryInterface
{
    public function criar(Classe $classe): Classe;
    public function buscarPorId(int $id, int $mundoId): ?Classe;
    public function buscarPorSlug(string $slug, int $mundoId): ?Classe;
    public function listarPorMundo(int $mundoId, int $offset = 0): array;
    public function atualizar(Classe $classe): bool;
    public function excluir(int $id, int $mundoId): bool;
    public function possuiPersonagens(int $id): bool;
    /**
     * atributos vinculados a uma classe.
     *
     * @param int $classeId
     * @param \App\Domain\Classe\ClasseAtributo[] $atributos
     * @return \App\Domain\Classe\ClasseAtributo[] $atributos
     */
    public function vincularAtributos(int $classeId, array $atributos): array;
    public function atualizarAtributos(int $classeId, array $atributos): void;
    /**
     * Busca os atributos vinculados a uma classe.
     *
     * @param int $classeId
     * @return \App\Domain\Classe\ClasseAtributo[]
     */
    public function buscarAtributos(int $classeId): array;
}
