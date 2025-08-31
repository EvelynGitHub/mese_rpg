<?php

use App\Http\Middleware\JWTAuthMiddleware;
use App\Http\Middleware\NoAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
})->middleware(NoAuthMiddleware::class);

Route::get('/home', function () {
    return view('home');
})->middleware(JWTAuthMiddleware::class);

Route::get('/mundo/{mundoId}/jogador', function ($mundoId) {
    return view('mundo-jogador', ["mundo_id" => $mundoId]);
})->middleware(JWTAuthMiddleware::class);

Route::get('/mundo/{mundoId}/mestre', function ($mundoId) {
    return view('mundo-mestre', ["mundo_id" => $mundoId]);
})->middleware(JWTAuthMiddleware::class);

Route::get('/painel-mestre/{mundoId}', function ($mundoId) {
    return view('mundo-mestre', ["mundo_id" => $mundoId]);
})->middleware(JWTAuthMiddleware::class);

Route::get('/habilidades/{mundoId}', function ($mundoId) {
    // $id = $request->get('mundo') ?? $mundoId; // Valor encriptado
    // $id = $request->mundoIdCriptografado;
    return view('mestre/habilidades', ["mundo_id" => $mundoId]);
})->middleware(JWTAuthMiddleware::class);

Route::get('/atributos/{mundoId}', function ($mundoId) {
    return view('mestre/atributos', ["mundo_id" => $mundoId]);
})->middleware(JWTAuthMiddleware::class);

Route::get('/campanhas/{mundoId}', function ($mundoId) {
    return view('mestre/campanhas', ["mundo_id" => $mundoId]);
})->middleware(JWTAuthMiddleware::class);

Route::get('/classes/{mundoId}', function ($mundoId) {
    return view('mestre/classes', ["mundo_id" => $mundoId]);
})->middleware(JWTAuthMiddleware::class);

Route::get('/itens-armas/{mundoId}', function ($mundoId) {
    return view('mestre/itens', ["mundo_id" => $mundoId]);
})->middleware(JWTAuthMiddleware::class);

Route::get('/npcs/{mundoId}', function ($mundoId) {
    return view('mestre/npcs', ["mundo_id" => $mundoId]);
})->middleware(JWTAuthMiddleware::class);

Route::get('/origens/{mundoId}', function ($mundoId) {
    return view('mestre/origens', ["mundo_id" => $mundoId]);
})->middleware(JWTAuthMiddleware::class);

Route::get('/personagens/{mundoId}', function ($mundoId) {
    return view('mestre/personagens', ["mundo_id" => $mundoId]);
})->middleware(JWTAuthMiddleware::class);
