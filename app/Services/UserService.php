<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = false;

        $user = $this->userRepository->create($data);
        $user->assignRole('user');

        return $user;
    }

    public function login(array $credentials): ?string
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if ($user && $user->is_active && Auth::attempt($credentials)) {
            return Auth::user()->createToken('LoginToken')->plainTextToken;
        }

        return null;
    }

    public function logout(User $user): void
    {
        $this->userRepository->revokeTokens($user);
    }

    public function updateProfile(User $user, array $data): ?User
    {
        if (isset($data['current_password']) && !Hash::check($data['current_password'], $user->password)) {
            return null;
        }

        if (isset($data['new_password'])) {
            $data['password'] = Hash::make($data['new_password']);
            unset($data['new_password']);
        }

        $this->userRepository->update($user, $data);

        return $user;
    }

    public function deleteProfile(User $user, string $password): bool
    {
        if (!Hash::check($password, $user->password)) {
            return false;
        }

        $user->tokens()->delete();
        return $this->userRepository->delete($user);
    }
}
