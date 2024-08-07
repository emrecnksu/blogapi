<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController
{
    use ResponseTrait;

    public function register(RegisterRequest $request)
    {
        $requestValidated = $request->validated();

        $user = User::create([
            'name' => $requestValidated['name'],
            'surname' => $requestValidated['surname'],
            'email' => $requestValidated['email'],
            'password' => Hash::make($requestValidated['password']),
            'is_active' => false,
        ]);

        $user->assignRole('user');

        return $this->successResponse(new UserResource($user), 'Kullanıcı kaydı başarıyla gerçekleştirildi ve admin onayı bekliyor.');
    }

    public function login(LoginRequest $request)
    {
        $requestValidated = $request->validated();

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $requestValidated['email'])->first();

        if ($user && $user->is_active && Auth::attempt($credentials)) {
            $token = Auth::user()->createToken('LoginToken')->plainTextToken;

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $token,
            ], 'Giriş işlemi başarıyla gerçekleşti!');
        } else {
            return $this->errorResponse('E-posta adresi veya şifre yanlış ya da kullanıcı pasif durumda!', 401);
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse(null, 'Başarıyla çıkış yapıldı.');
        } else {
            return $this->errorResponse('Oturumunuz açık değil. İlk önce oturum açmalısınız!', 401);
        }
    }
}


// namespace App\Http\Controllers\Auth;

// use App\Models\User;
// use Illuminate\Http\Request;
// use App\Http\Requests\LoginRequest;
// use App\Http\Resources\UserResource;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
// use App\Http\Requests\RegisterRequest;
// use Illuminate\Support\Facades\Session;

// class UserController
// {
//     public function register(RegisterRequest $request)
//     {
//         $requestValidated = $request->validated();

//         $user = User::create([
//             'name' => $requestValidated['name'],
//             'surname' => $requestValidated['surname'],
//             'email' => $requestValidated['email'],
//             'password' => Hash::make($requestValidated['password']),
//             'is_active' => false,
//         ]);

//         $user->assignRole('user');

//         return (new UserResource($user))->additional(['message' => 'Kullanıcı kaydı başarıyla gerçekleştirildi ve admin onayı bekliyor.']);
//     }

//     public function login(LoginRequest $request)
//     {
//         $requestValidated = $request->validated();

//         $credentials = $request->only('email', 'password');
//         $user = User::where('email', $requestValidated['email'])->first();

//         if ($user && $user->is_active && Auth::attempt($credentials)) {
//             $token = Auth::user()->createToken('LoginToken')->plainTextToken;

//             return response()->json([
//                 'data' => [
//                     'user' => new UserResource($user),
//                     'token' => $token,
//                 ],
//                 'message' => 'Giriş işlemi başarıyla gerçekleşti!',
//             ]);
//         } else {
//             return response()->json([
//                 'error' => 'E-posta adresi veya şifre yanlış ya da kullanıcı pasif durumda!'
//             ], 401);
//         }
//     }

//     public function logout(Request $request)
//     {
//         if ($request->user()) {
//             $request->user()->currentAccessToken()->delete();
//             return response()->json(['message' => 'Başarıyla çıkış yapıldı.'], 200);
//         } else {
//             return response()->json(['message' => 'Oturumunuz açık değil. İlk önce oturum açmalısınız!'], 401);
//         }
//     }
// }
