<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class DecryptMundoIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $mundoIdCriptografado = $request->route('mundoId') ?? $request->query('mundo');

        if ($mundoIdCriptografado) {
            try {
                $mundoId = Crypt::decryptString($mundoIdCriptografado);
                if (!empty($mundoId) && is_numeric($mundoId)) {
                    $request->route()->setParameter('mundoId', (int) $mundoId);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => 'Mundo ID inválido ou não encontrado.'], 404);
            }
        }

        return $next($request);
    }
}
