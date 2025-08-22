<?php
namespace App\Http\Controllers;

use App\UseCases\BuscarLivrosDestaque;
use Illuminate\Http\JsonResponse;

class LivrosController extends Controller
{
    private BuscarLivrosDestaque $buscarLivrosDestaque;

    public function __construct(BuscarLivrosDestaque $buscarLivrosDestaque)
    {
        $this->buscarLivrosDestaque = $buscarLivrosDestaque;
    }

    public function index(): JsonResponse
    {
        $livros = $this->buscarLivrosDestaque->execute();
        return response()->json($livros);
    }
}
