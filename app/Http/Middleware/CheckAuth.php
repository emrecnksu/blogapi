<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
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
        $token = $request->bearerToken();
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken && $accessToken->tokenable) {
                Auth::setUser($accessToken->tokenable);
            }
        }

        if (!Auth::check()) {
            $routeName = $request->route()->getName();
            $unauthorizedMessage = 'Bu işlemi yapmak için giriş yapmalısınız!';
            
            $unauthorizedMessages = [
                'comments.store' => 'Yorum yapabilmek için giriş yapmalısınız.',
                'comments.approve' => 'Yorumu onaylamak için giriş yapmalısınız!',
                'comments.update' => 'Yorumu güncelleyebilmek için giriş yapmalısınız!',
                'comments.delete' => 'Yorumu silebilmek için giriş yapmalısınız!',
                'logout' => 'Çıkış yapabilmek için giriş yapmalısınız!',
                'profile.update' => 'Profili güncelleyebilmek için giriş yapmalısınız!',
                'profile.show' => 'Profili görebilmek için giriş yapmalısınız!',
                'profile.delete' => 'Profili silebilmek için giriş yapmalısınız!',
            ];

            if (array_key_exists($routeName, $unauthorizedMessages)) {
                $unauthorizedMessage = $unauthorizedMessages[$routeName];
            }

            return response()->json(['status' => 0, 'message' => $unauthorizedMessage], 401);
        }

        return $next($request);
    }
}
