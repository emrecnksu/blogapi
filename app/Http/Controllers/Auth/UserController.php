<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:4|confirmed',
        ], [
            'name.required' => 'Ad alanı boş bırakılmamalıdır',
            'surname.required' => 'Soyad alanı boş bırakılmamalıdır',
            'email.required' => 'E-posta alanı boş bırakılmamalıdır',
            'email.unique' => 'Bu E-posta adresi zaten kullanılmaktadır',
            'password.required' => 'Şifre alanı boş bırakılmamalıdır',
            'password.min' => 'Şifre en az 4 karakter olmalıdır',
            'password.confirmed' => 'Şifreler eşleşmiyor',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->first()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => false,
        ]);

        $user->assignRole('user');

        return response()->json([
            'status' => 1,
            'name' => $user->name,
            'surname' => $user->surname,
            'message' => 'Kullanıcı kaydı başarıyla gerçekleştirildi ve admin onayı bekliyor.'
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'E-posta alanı boş bırakılmamalıdır.',
            'email.email' => 'Geçersiz e-posta adresi.',
            'password.required' => 'Şifre alanı boş bırakılmamalıdır.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()], 400);
        }

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();

        if ($user && $user->is_active && Auth::attempt($credentials)) {
            $token = Auth::user()->createToken('LoginToken')->plainTextToken;

            // Add user information to session //
            Session::put('token', $token);
            Session::put('user_id', $user->id); 
            Session::put('name', $user->name);
            Session::put('surname', $user->surname);

            return response()->json([
                'status' => 1,
                'user_id' => $user->id,
                'name' => $user->name,
                'surname' => $user->surname,
                'token' => $token,
                'message' => 'Giriş işlemi başarıyla gerçekleşti!'
            ], 200);
        } else {
            return response()->json(['status' => 0, 'error' => 'E-posta adresi veya şifre yanlış ya da kullanıcı pasif durumda!'], 401);
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
