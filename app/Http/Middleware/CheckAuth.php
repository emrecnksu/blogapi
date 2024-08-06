<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            $routeName = $request->route()->getName();

            if ($routeName === 'comments.store') {
                return response()->json(['status' => 0, 'message' => 'Yorum yapabilmek için giriş yapmalısınız.'], 401);
            } elseif (in_array($routeName, ['profile.update', 'profile.show', 'profile.delete'])) {
                return response()->json(['status' => 0, 'message' => 'Profili güncelleyebilmek için giriş yapmalısınız!'], 401);
            }
        }

        return $next($request);
    }
}
