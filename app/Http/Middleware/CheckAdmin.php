<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
{
    $user = $request->user();

    // Проверка, что юзер авторизован и его роль 1, 2 или 3
    if (!$user || !in_array((int)$user->role, [1, 2, 3])) {
        return response()->json(['message' => 'Доступ запрещен. Нужны права админа.'], 403);
    }

    return $next($request);
}
}
