<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Services\Api\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle user login.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->login($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'token' => $data['token'],
                    'user' => $data['user'],
                    'role' => $data['role'],
                ],
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->errors()['email'][0] ?? 'Kredensial tidak valid.',
            ], 401);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * Get authenticated user data.
     */
    public function user(Request $request): JsonResponse
    {
        $userData = $this->authService->getProfile($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diambil',
            'data' => $userData,
        ]);
    }
}
