<?php

namespace App\Services\Api\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthService
{
    /**
     * Authenticate the user and generate a Sanctum token.
     */
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
            ]);
        }

        $user = Auth::user();

        // Load specific profile depending on role
        if ($user->hasRole('siswa')) {
            $user->load('student');
        } elseif ($user->hasRole('guru') || $user->hasRole('staff')) {
            $user->load('employee');
        }

        // Generate Sanctum token
        // Token name could be the device name, but we'll use 'mobile-app' as a standard
        $token = $user->createToken('mobile-app')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'role' => $user->roles->pluck('name')->first() ?? 'user'
        ];
    }

    /**
     * Revoke tokens for the authenticated user.
     */
    public function logout(User $user)
    {
        // Revoke the token that was used to authenticate the current request
        $user->currentAccessToken()->delete();
        return true;
    }

    /**
     * Get authenticated user profile logic.
     */
    public function getProfile(User $user)
    {
        if ($user->hasRole('siswa')) {
            $user->load('student');
        } elseif ($user->hasRole('guru') || $user->hasRole('staff')) {
            $user->load('employee');
        }

        $user->roles; // Load roles collection explicitly

        return collect($user)->merge([
            'role_name' => $user->roles->pluck('name')->first()
        ]);
    }
}
