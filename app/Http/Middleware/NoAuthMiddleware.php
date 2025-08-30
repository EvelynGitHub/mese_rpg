<?php

namespace App\Http\Middleware;

use App\Domain\Auth\JWTService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoAuthMiddleware
{
    private JWTService $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $cookieHeader = $request->header('Cookie');
            $token = null;

            if ($cookieHeader) {
                preg_match('/jwt_token=([^;]+)/', $cookieHeader, $matches);
                if (isset($matches[1])) {
                    $token = $matches[1];
                }
            }

            if (!$token) {
                return $next($request);
            }

            $payload = $this->jwtService->validateToken($token);
            $request->auth = $payload;

            // return redirect('/home');
            return response()->json([
                'message' => 'Entrando...',
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Token inválido ou expirado.',
                'tracer' => $e->getMessage()
            ], 401);
        }
    }
}
