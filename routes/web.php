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


Route::get('/habilidades/{mundoId}', function ($mundoId, Request $request) {
    $id = $request->get('mundo') ?? $mundoId; // Valor encriptado
    return view('mestre/habilidades', ["mundo_id" => $id]);
})->middleware(JWTAuthMiddleware::class);
