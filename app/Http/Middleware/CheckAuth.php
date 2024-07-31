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
        if (!Auth::check()) {
            return response()->json(['status' => 0, 'message' => 'Yorum yapabilmek için giriş yapmalısınız.'], 401);
        }

        return $next($request);
    }
}


// public function handle(Request $request, Closure $next): Response
//     {
//         // Kullanıcı oturumunu kontrol et ve logla
//         if (!Auth::check()) {
//             Log::warning('Kullanıcı oturumu açık değil.', ['user' => null]);
//             return response()->json(['status' => 0, 'message' => 'Yorum yapabilmek için giriş yapmalısınız.'], 401);
//         }

//         // Kullanıcı oturumu açık, kullanıcı bilgilerini logla
//         $user = Auth::user();
//         Log::info('Kullanıcı oturumu açık.', ['user' => $user]);

//         return $next($request);
//     }
