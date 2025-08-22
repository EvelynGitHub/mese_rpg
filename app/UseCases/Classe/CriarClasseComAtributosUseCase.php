<?php

namespace App\UseCases\Classe;

use App\Domain\Classe\Classe;
use App\Domain\Classe\ClasseAtributo;
use App\Repositories\Interfaces\ClasseRepositoryInterface;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

class CriarClasseComAtributosUseCase
{
    private ClasseRepositoryInterface $classeRepository;

    public function __construct(ClasseRepositoryInterface $classeRepository)
    {
        $this->classeRepository = $classeRepository;
    }

    /**
     * @param array $atributosData Formato:
     * [
     *   [
     *     'atributo_id' => int,
     *     'tipo_dado_id' => int|null,
     *     'base_fixa' => int,
     *     'limite_base_fixa' => int|null,
     *     'limite_tipo_dado_id' => int|null,
     *     'imutavel' => bool
     *   ],
     *   ...
     * ]
     */
    public function executar(
        int $mundoId,
        string $slug,
        string $nome,
        ?string $descricao = null,
        array $atributosData = []
    ): Classe {
        // Validar slug
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            throw new InvalidArgumentException('Slug deve conter apenas letras minúsculas, números e hífens');
        }

        // Verificar se já existe classe com mesmo slug no mundo
        if ($this->classeRepository->buscarPorSlug($slug, $mundoId)) {
            throw new InvalidArgumentException('Já existe uma classe com este slug neste mundo');
        }

        return DB::transaction(function () use ($mundoId, $slug, $nome, $descricao, $atributosData) {
            // Criar classe
            $classe = new Classe($mundoId, $slug, $nome, $descricao);
            $classe = $this->classeRepository->criar($classe);

            // Criar atributos da classe
            foreach ($atributosData as $data) {
                $atributo = new ClasseAtributo(
                    $data['atributo_id'],
                    $data['tipo_dado_id'] ?? null,
                    $data['base_fixa'] ?? 0,
                    $data['limite_base_fixa'] ?? null,
                    $data['limite_tipo_dado_id'] ?? null,
                    $data['imutavel'] ?? true
                );
                $classe->adicionarAtributo($atributo);
            }

            // Vincular atributos
            if (!empty($classe->getAtributos())) {
                $this->classeRepository->vincularAtributos($classe->getId(), $classe->getAtributos());
            }

            return $classe;
        });
    }
}
