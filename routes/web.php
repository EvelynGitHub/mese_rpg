<?php

use App\Http\Middleware\JWTAuthMiddleware;
use App\Http\Middleware\NoAuthMiddleware;
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
