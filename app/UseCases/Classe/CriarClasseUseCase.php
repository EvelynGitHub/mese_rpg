<?php

namespace App\UseCases\Classe;

use App\Domain\Classe\Classe;
use App\Repositories\Interfaces\ClasseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CriarClasseUseCase
{
    private ClasseRepositoryInterface $classeRepository;

    public function __construct(ClasseRepositoryInterface $classeRepository)
    {
        $this->classeRepository = $classeRepository;
    }

    public function executar(
        int $mundoId,
        string $slug,
        string $nome,
        int $criadoPor,
        ?string $descricao = null
    ): Classe {
        if ($this->classeRepository->buscarPorSlug($slug, $mundoId)) {
            throw new \InvalidArgumentException('Já existe uma classe com este slug neste mundo');
        }

        $classe = new Classe(
            $mundoId,
            $slug,
            $nome,
            $descricao,
        );

        return DB::transaction(function () use ($classe, $criadoPor) {
            $classeSalva = $this->classeRepository->criar($classe);

            DB::table('auditoria')->insert([
                'evento' => 'CRIAR_CLASSE',
                'usuario_id' => $criadoPor,
                'mundo_id' => $classe->getMundoId(),
                'payload_before' => null,
                'payload_after' => json_encode([
                    'id' => $classeSalva->getId(),
                    'slug' => $classe->getSlug(),
                    'nome' => $classe->getNome(),
                    'descricao' => $classe->getDescricao()
                ]),
                'criado_em' => now()
            ]);

            return $classeSalva;
        });
    }
}
