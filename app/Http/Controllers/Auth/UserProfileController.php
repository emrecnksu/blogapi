<?php

namespace App\Http\Controllers\Auth;

use Exception;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserProfileRequest;

class UserProfileController
{
    use ResponseTrait;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function update(UserProfileRequest $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->errorResponse('Kullanıcı bulunamadı', 404);
            }

            $updatedUser = $this->userService->updateProfile($user, $request->validated());

            if (!$updatedUser) {
                return $this->errorResponse('Mevcut şifre yanlış.', 400);
            }

            return $this->successResponse(new UserResource($updatedUser), 'Profil başarıyla güncellendi.');
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

        $isDeleted = $this->userService->deleteProfile($user, $requestValidated['delete_password']);

        if (!$isDeleted) {
            return $this->errorResponse('Mevcut şifre yanlış.', 400);
        }

        return $this->successResponse(null, 'Hesabınız başarıyla silindi.');
    }
}
