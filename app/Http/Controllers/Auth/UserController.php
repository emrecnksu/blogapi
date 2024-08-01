<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Session;

class UserController
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => false,
        ]);

        $user->assignRole('user');

        return (new UserResource($user))->additional(['message' => 'Kullanıcı kaydı başarıyla gerçekleştirildi ve admin onayı bekliyor.']);
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $validated['email'])->first();

        if ($user && $user->is_active && Auth::attempt($credentials)) {
            $token = Auth::user()->createToken('LoginToken')->plainTextToken;

            return response()->json([
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ],
                'message' => 'Giriş işlemi başarıyla gerçekleşti!',
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'error' => 'E-posta adresi veya şifre yanlış ya da kullanıcı pasif durumda!'
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Başarıyla çıkış yapıldı.'], 200);
        } else {
            return response()->json(['message' => 'Oturumunuz açık değil. İlk önce oturum açmalısınız!'], 401);
        }
    }
}
