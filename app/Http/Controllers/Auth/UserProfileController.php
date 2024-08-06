<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserProfileRequest;

class UserProfileController
{
    public function update(UserProfileRequest $request)
    {
        try {
            $requestValidated = $request->validated();
            $user = Auth::user();

            if (!$user) {
                return response()->json(['status' => 0, 'message' => 'Kullanıcı bulunamadı'], 404);
            }

            if (isset($requestValidated['current_password']) && !Hash::check($requestValidated['current_password'], $user->password)) {
                return response()->json(['status' => 0, 'error' => 'Mevcut şifre yanlış.'], 400);
            }

            $user->update([
                'name' => $requestValidated['name'] ?? $user->name,
                'surname' => $requestValidated['surname'] ?? $user->surname,
                'email' => $requestValidated['email'] ?? $user->email,
                'password' => isset($requestValidated['new_password']) ? Hash::make($requestValidated['new_password']) : $user->password,
            ]);

            return (new UserResource($user))->additional(['message' => 'Profil başarıyla güncellendi.']);
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

        return new UserResource($user); 
    }

    public function delete(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 0, 'error' => 'Kullanıcı bulunamadı.'], 404);
        }

        $requestValidated = $request->validate([
            'delete_password' => 'required|string|min:4',
        ]);

        if (!Hash::check($requestValidated['delete_password'], $user->password)) {
            return response()->json(['status' => 0, 'error' => 'Mevcut şifre yanlış.'], 400);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['status' => 1, 'message' => 'Hesabınız başarıyla silindi.'], 200);
    }
}
