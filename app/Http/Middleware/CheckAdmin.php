<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Обработка входящего запроса.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Список разрешенных строковых ролей
        $allowedRoles = ['admin', 'superadmin', 'moderator'];

        // Проверяем, авторизован ли пользователь и входит ли его роль в список разрешенных
        if (!$user || !in_array($user->role, $allowedRoles)) {
            return response()->json([
                'message' => 'Доступ запрещен. Требуются права администратора или модератора.',
                'your_role' => $user ? $user->role : 'guest'
            ], 403);
        }

        return $next($request);
    }
}
