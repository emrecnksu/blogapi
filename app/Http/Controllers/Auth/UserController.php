<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Traits\ResponseTrait;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;

class UserController
{
    use ResponseTrait;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->userService->register($request->validated());

        return $this->successResponse(new UserResource($user), 'Kullanıcı kaydı başarıyla gerçekleştirildi ve admin onayı bekliyor.');
    }

    public function login(LoginRequest $request)
    {
        $token = $this->userService->login($request->validated());

        if ($token) {
            $user = Auth::user();
            
            return $this->successResponse(['user' => new UserResource($user), 'token' => $token], 'Giriş işlemi başarıyla gerçekleşti!');
        }

        return $this->errorResponse('E-posta adresi veya şifre yanlış ya da kullanıcı pasif durumda!', 401);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $this->userService->logout($user);
            return $this->successResponse(null, 'Başarıyla çıkış yapıldı.');
        } else {
            return $this->errorResponse('Oturumunuz açık değil ya da token bulunamadı. İlk önce oturum açmalısınız!', 401);
        }
    }
}
