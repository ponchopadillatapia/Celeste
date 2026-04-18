<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Valida que el usuario autenticado tenga el rol requerido.
     * Uso: ->middleware('role:admin') o ->middleware('role:admin,residente')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        if (!in_array($user->role, $roles)) {
            return response()->json(['message' => 'No tienes permisos para esta acción.'], 403);
        }

        return $next($request);
    }
}
