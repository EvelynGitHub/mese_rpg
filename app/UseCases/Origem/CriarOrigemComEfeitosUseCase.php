<?php

namespace App\UseCases\Origem;

use App\Domain\Origem\Origem;
use App\Domain\Origem\OrigemEfeito;
use App\Repositories\Interfaces\OrigemRepositoryInterface;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

class CriarOrigemComEfeitosUseCase
{
    private OrigemRepositoryInterface $origemRepository;

    public function __construct(OrigemRepositoryInterface $origemRepository)
    {
        $this->origemRepository = $origemRepository;
    }

    /**
     * @param array $efeitosData Formato:
     * [
     *   [
     *     'tipo' => string,
     *     'atributo_id' => int|null,
     *     'delta' => int|null,
     *     'notas' => array|null
     *   ],
     *   ...
     * ]
     */
    public function executar(
        int $mundoId,
        string $slug,
        string $nome,
        ?string $descricao = null,
        array $efeitosData = []
    ): Origem {
        // Validar slug
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            throw new InvalidArgumentException('Slug deve conter apenas letras minúsculas, números e hífens');
        }

        // Verificar se já existe origem com mesmo slug no mundo
        if ($this->origemRepository->buscarPorSlug($slug, $mundoId)) {
            throw new InvalidArgumentException('Já existe uma origem com este slug neste mundo');
        }

        return DB::transaction(function () use ($mundoId, $slug, $nome, $descricao, $efeitosData) {
            // Criar origem
            $origem = new Origem($mundoId, $slug, $nome, $descricao);
            $origem = $this->origemRepository->criar($origem);

            // Criar efeitos
            foreach ($efeitosData as $data) {
                $efeito = new OrigemEfeito(
                    $data['tipo'],
                    $data['atributo_id'] ?? null,
                    $data['delta'] ?? null,
                    $data['notas'] ?? null
                );
                $origem->adicionarEfeito($efeito);
            }

            // Vincular efeitos
            if (!empty($origem->getEfeitos())) {
                $this->origemRepository->vincularEfeitos($origem->getId(), $origem->getEfeitos());
            }

            return $origem;
        });
    }
}
