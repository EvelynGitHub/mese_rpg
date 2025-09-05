<?php

namespace App\Http\Middleware;

use App\Domain\Auth\JWTService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class JWTAuthWebMiddleware
{
    private JWTService $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        try {
            // $token = $request->cookie('jwt_token'); // Não funciona

            $cookieHeader = $request->header('Cookie');
            $token = null;

            if ($cookieHeader) {
                preg_match('/jwt_token=([^;]+)/', $cookieHeader, $matches);
                if (isset($matches[1])) {
                    $token = $matches[1];
                }
            }

            if (!$token) {
                return redirect('/login');
                // return response()->json(['message' => 'Token não fornecido ou inválido.'], 401);
            }

            $payload = $this->jwtService->validateToken($token);
            $request->auth = $payload;

            if (!empty($roles)) {
                $papeisDoUsuario = (array) $payload['papeis_por_mundo'] ?? [];
                $mundoId = $request->route('mundoId');

                if ($mundoId && isset($papeisDoUsuario[$mundoId])) {
                    $papelNoMundo = $papeisDoUsuario[$mundoId];
                    if (!in_array($papelNoMundo, $roles)) {
                        return response()->json(['message' => 'Acesso não autorizado. Papel insuficiente.'], 403);
                    }
                } else {
                    // Mundo ou papel não encontrado no token
                    return response()->json(['message' => 'Acesso não autorizado. Informações de mundo ausentes.'], 403);
                }
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token inválido ou expirado.',
                'tracer' => $e->getMessage()
            ], 401);
            // return redirect('/login');
        }
    }
}
