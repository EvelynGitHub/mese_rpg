<?php

use App\Http\Controllers\AtributoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampanhaController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\HabilidadeController;
use App\Http\Controllers\MundoController;
use App\Http\Controllers\OrigemController;
use App\Http\Controllers\NpcController;
use App\Http\Controllers\PersonagemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rotas públicas de autenticação
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:jwt')->group(function () {
    // Rota autenticada de auth
    Route::get('auth/me', [AuthController::class, 'me']);

    // Rotas de Mundo
    Route::prefix('mundos')->group(function () {
        Route::post('/', [MundoController::class, 'criar']);
        Route::get('/', [MundoController::class, 'listar']);

        Route::middleware('role:admin')->group(function () {
            Route::patch('/{id}', [MundoController::class, 'atualizar']);
            Route::delete('/{id}', [MundoController::class, 'excluir']);
            Route::post('/{id}/membros', [MundoController::class, 'adicionarMembro']);
        });

        Route::middleware('role:admin,mestre')->group(function () {
            Route::get('/{id}/regras', [MundoController::class, 'obterRegras']);
            Route::put('/{id}/regras', [MundoController::class, 'atualizarRegras'])
                ->middleware('role:admin');

            // Rotas de Atributo
            Route::prefix('{mundoId}/atributos')->group(function () {
                Route::get('/', [AtributoController::class, 'listar']);
                Route::get('/{id}', [AtributoController::class, 'buscarPorId']);

                Route::middleware('role:admin,mestre')->group(function () {
                    Route::post('/', [AtributoController::class, 'criar']);
                    Route::patch('/{id}', [AtributoController::class, 'atualizar']);
                    Route::delete('/{id}', [AtributoController::class, 'excluir']);
                });
            });

            // Rotas de Classe
            Route::prefix('{mundoId}/classes')->group(function () {
                Route::get('/', [ClasseController::class, 'listar']);
                Route::get('/{id}', [ClasseController::class, 'buscarPorId']);

                Route::middleware('role:admin,mestre')->group(function () {
                    Route::post('/', [ClasseController::class, 'criar']);
                    Route::patch('/{id}', [ClasseController::class, 'atualizar']);
                    Route::delete('/{id}', [ClasseController::class, 'excluir']);

                    // Rotas de atributos da classe
                    Route::post('/{classeId}/atributos', [ClasseController::class, 'adicionarAtributo']);
                });
            });

            // Rotas de Campanha
            Route::prefix('{mundoId}/campanhas')->group(function () {
                Route::post('/', [CampanhaController::class, 'criar']);
                Route::patch('/{id}', [CampanhaController::class, 'atualizar']);
                Route::delete('/{id}', [CampanhaController::class, 'excluir']);
            });

            // Rotas de Origem
            Route::prefix('{mundoId}/origens')->group(function () {
                Route::get('/', [OrigemController::class, 'listar']);
                Route::get('/{id}', [OrigemController::class, 'buscar']);
                Route::get('/{origemId}/efeitos', [OrigemController::class, 'listarEfeitos']);

                Route::middleware('role:admin,mestre')->group(function () {
                    Route::post('/', [OrigemController::class, 'criar']);
                    Route::patch('/{id}', [OrigemController::class, 'atualizar']);
                    Route::delete('/{id}', [OrigemController::class, 'excluir']);

                    // Rotas de efeitos da origem
                    Route::post('/{origemId}/efeitos', [OrigemController::class, 'criarEfeito']);
                    Route::patch('/{origemId}/efeitos/{efeitoId}', [OrigemController::class, 'atualizarEfeito']);
                    Route::delete('/{origemId}/efeitos/{efeitoId}', [OrigemController::class, 'excluirEfeito']);
                });
            });

            // Rotas de Habilidade
            Route::prefix('{mundoId}/habilidades')->group(function () {
                Route::get('/', [HabilidadeController::class, 'listar']);
                Route::get('/{id}', [HabilidadeController::class, 'buscar']);

                Route::middleware('role:admin,mestre')->group(function () {
                    Route::post('/', [HabilidadeController::class, 'criar']);
                    Route::patch('/{id}', [HabilidadeController::class, 'atualizar']);
                    Route::delete('/{id}', [HabilidadeController::class, 'excluir']);
                });
            });

            // Rotas de vinculação de habilidades
            Route::prefix('{mundoId}/classes')->middleware('role:admin,mestre')->group(function () {
                Route::get('/{classeId}/habilidades', [HabilidadeController::class, 'listarPorClasse']);
                Route::post('/{classeId}/habilidades/{habilidadeId}', [HabilidadeController::class, 'vincularClasse']);
                Route::delete('/{classeId}/habilidades/{habilidadeId}', [HabilidadeController::class, 'desvincularClasse']);
            });

            Route::prefix('{mundoId}/origens')->middleware('role:admin,mestre')->group(function () {
                Route::get('/{origemId}/habilidades', [HabilidadeController::class, 'listarPorOrigem']);
                Route::post('/{origemId}/habilidades/{habilidadeId}', [HabilidadeController::class, 'vincularOrigem']);
                Route::delete('/{origemId}/habilidades/{habilidadeId}', [HabilidadeController::class, 'desvincularOrigem']);
            });

            // Rotas de NPC
            Route::prefix('{mundoId}/npcs')->group(function () {
                // Rotas acessíveis por qualquer membro do mundo
                Route::get('/', [NpcController::class, 'listar']);
                Route::get('/{id}', [NpcController::class, 'buscar']);

                // Rotas restritas a admin/mestre
                Route::middleware('role:admin,mestre')->group(function () {
                    Route::post('/', [NpcController::class, 'criar']);
                    Route::patch('/{id}', [NpcController::class, 'atualizar']);
                    Route::delete('/{id}', [NpcController::class, 'deletar']);
                });
            });
        });

        // Rotas de Personagem
        Route::prefix('{mundoId}/personagens')->group(function () {
            Route::get('/', [PersonagemController::class, 'listar']);
            Route::get('/{id}', [PersonagemController::class, 'buscar']);
            Route::post('/', [PersonagemController::class, 'criar']);
            Route::patch('/{id}', [PersonagemController::class, 'atualizar']);
            
            // Rotas de ação em personagem
            Route::post('/{id}/reset-alocacao', [PersonagemController::class, 'resetarAlocacao'])
                ->middleware('role:admin,mestre');
            Route::post('/{id}/equipar-item', [PersonagemController::class, 'equiparItem']);
        });

        // Rotas de consulta de campanha - qualquer membro do mundo pode acessar
        Route::prefix('{mundoId}/campanhas')->group(function () {
            Route::get('/', [CampanhaController::class, 'listar']);
            Route::get('/{id}', [CampanhaController::class, 'buscarPorId']);
        });
    });
});
