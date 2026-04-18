<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionActive
{
    /**
     * Valida que el token actual del usuario siga activo.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Sesión no activa.'], 401);
        }

        // Verificar que el token actual existe y no ha sido revocado
        $token = $user->currentAccessToken();
        if (!$token || ($token->expires_at && $token->expires_at->isPast())) {
            return response()->json(['message' => 'Sesión expirada.'], 401);
        }

        return $next($request);
    }
}
