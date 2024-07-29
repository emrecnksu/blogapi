<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserProfileController
{
    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['status' => 0, 'message' => 'Kullanıcı bulunamadı'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'surname' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
                'current_password' => 'sometimes|required|string|min:4',
                'new_password' => 'sometimes|required|string|min:4|confirmed|different:current_password',
            ], [
                'name.required' => 'Ad alanı boş bırakılmamalıdır.',
                'surname.required' => 'Soyad alanı boş bırakılmamalıdır.',
                'email.required' => 'E-posta alanı boş bırakılmamalıdır.',
                'email.email' => 'Geçersiz e-posta adresi.',
                'email.unique' => 'Bu E-posta adresi zaten kullanılmaktadır.',
                'current_password.min' => 'Mevcut şifre en az 4 karakter olmalıdır.',
                'current_password.required' => 'Mevcut şifre alanı boş bırakılmamalıdır.',
                'new_password.required' => 'Yeni şifre alanı boş bırakılmamalıdır.',
                'new_password.min' => 'Yeni şifre en az 4 karakter olmalıdır.',
                'new_password.confirmed' => 'Yeni şifreler eşleşmiyor.',
                'new_password.different' => 'Yeni şifre mevcut şifre ile aynı olamaz.',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 0, 'error' => $validator->errors()], 400);
            }

            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('surname')) {
                $user->surname = $request->surname;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('current_password') && $request->has('new_password')) {
                if (Hash::check($request->current_password, $user->password)) {
                    $user->password = Hash::make($request->new_password);
                } else {
                    return response()->json(['status' => 0, 'error' => 'Mevcut şifre yanlış.'], 400);
                }
            }

            $user->save();

            Session::put('name', $user->name);
            Session::put('surname', $user->surname);

            return response()->json(['status' => 1, 'message' => 'Profil başarıyla güncellendi!']);
        } catch (Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return response()->json(['status' => 0, 'error' => 'Profil güncellenirken bir hata oluştu.'], 500);
        }
    }


    public function show()
    {
        if (!auth()->check()) {
            return response()->json(['status' => 0, 'message' => 'Bu işlemi yapmak için giriş yapmalısınız!'], 401);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'Kullanıcı bulunamadı'], 404);
        }

        return response()->json($user, 200);
    }

    public function delete(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 0, 'error' => 'Kullanıcı bulunamadı.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'delete_password' => 'required|string|min:4',
        ], [
            'delete_password.required' => 'Mevcut şifre alanı boş bırakılmamalıdır.',
            'delete_password.min' => 'Mevcut şifre en az 4 karakter olmalıdır.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()], 400);
        }

        if (!Hash::check($request->delete_password, $user->password)) {
            return response()->json(['status' => 0, 'error' => 'Mevcut şifre yanlış.'], 400);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['status' => 1, 'message' => 'Hesabınız başarıyla silindi.'], 200);
    }
}

