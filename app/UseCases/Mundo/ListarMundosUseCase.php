<?php

declare(strict_types=1);

namespace App\UseCases\Mundo;

use App\Repositories\Interfaces\MundoRepositoryInterface;
use Illuminate\Support\Facades\Crypt;

class ListarMundosUseCase
{
    // Mundos em que sou mestre
    private string $meusMundos = "meus_mundos";
    // Mundos em que sou jogador
    private string $outrosMundos = "outros_mundos";

    private MundoRepositoryInterface $mundoRepository;

    public function __construct(MundoRepositoryInterface $mundoRepository)
    {
        $this->mundoRepository = $mundoRepository;
    }

    public function executar(int $limit = 10, int $offset = 0, int $userId): array
    {
        // Busca mundos do $userId, onde ele é MESTRE e onde ele é JOGADOR
        $mundos = $this->mundoRepository->findAllByUserId($userId, $limit, $offset);

        if (empty($mundos)) {
            throw new \InvalidArgumentException('Você não tem nem faz parte de nenhum mundo ainda. Crie o seu!');
        }

        $resposta = [
            // $this->meusMundos => [],
            // $this->outrosMundos => [],
        ];

        foreach ($mundos as $mundo) {
            // $chave = $mundo->getCriadoPor() == $userId ? $this->meusMundos : $this->outrosMundos;
            // $resposta[$chave] = $mundo;

            $encryptedMundoId = Crypt::encryptString((string) $mundo->getId());
            $resposta[] = [
                "id" => $encryptedMundoId,
                "nome" => $mundo->getNome(),
                "descricao" => $mundo->getDescricao(),
                "criado_em" => $mundo->getCriadoEm()->format("d-m-Y"),
                "mestre" => $mundo->getCriadoPor() == $userId
            ];

        }

        return $resposta;
    }
}
