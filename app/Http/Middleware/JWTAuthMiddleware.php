<?php

namespace App\Http\Middleware;

use App\Domain\Auth\JWTService;
use Closure;
use Illuminate\Http\Request;

class JWTAuthMiddleware
{
    private JWTService $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token não fornecido'], 401);
        }

        try {
            $payload = $this->jwtService->validateToken($token);
            $request->attributes->set('jwt_payload', $payload);
            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        }
    }
}
