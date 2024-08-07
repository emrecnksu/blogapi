<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use App\Http\Requests\UserProfileRequest;
use App\Http\Resources\UserResource;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class UserProfileController
{
    use ResponseTrait;

    public function update(UserProfileRequest $request)
    {
        try {
            $requestValidated = $request->validated();
            $user = Auth::user();

            if (!$user) {
                return $this->errorResponse('Kullanıcı bulunamadı', 404);
            }

            if (isset($requestValidated['current_password']) && !Hash::check($requestValidated['current_password'], $user->password)) {
                return $this->errorResponse('Mevcut şifre yanlış.', 400);
            }

            $user->update([
                'name' => $requestValidated['name'] ?? $user->name,
                'surname' => $requestValidated['surname'] ?? $user->surname,
                'email' => $requestValidated['email'] ?? $user->email,
                'password' => isset($requestValidated['new_password']) ? Hash::make($requestValidated['new_password']) : $user->password,
            ]);

            return $this->successResponse(new UserResource($user), 'Profil başarıyla güncellendi.');
        } catch (Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return $this->errorResponse('Profil güncellenirken bir hata oluştu.', 500);
        }
    }

    public function show()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->errorResponse('Kullanıcı bulunamadı', 404);
        }

        return $this->successResponse(new UserResource($user));
    }

    public function delete(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return $this->errorResponse('Kullanıcı bulunamadı.', 404);
        }

        $requestValidated = $request->validate([
            'delete_password' => 'required|string|min:4',
        ]);

        if (!Hash::check($requestValidated['delete_password'], $user->password)) {
            return $this->errorResponse('Mevcut şifre yanlış.', 400);
        }

        $user->tokens()->delete();
        $user->delete();

        return $this->successResponse(null, 'Hesabınız başarıyla silindi.');
    }
}
