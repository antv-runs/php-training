<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Contracts\AuthServiceInterface;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    // Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $result = $this->authService->register($request->only(['name', 'email', 'password']));

            return response()->json([
                'success' => true,
                'message' => 'Register successfully',
                'data' => $result
            ], 201);
        } catch (\Throwable $e) {
            Log::error('AuthController::register error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $credentials = $request->only('email', 'password');
            $result = $this->authService->login($credentials);

            if (empty($result)) {
                return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
            }

            return response()->json(['success' => true, 'message' => 'Login successfully', 'data' => $result]);
        } catch (\Throwable $e) {
            Log::error('AuthController::login error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // LOGOUT
    public function logout(Request $request)
    {
        try {
            $ok = $this->authService->logout($request);
            if (! $ok) {
                return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
            }

            return response()->json(['success' => true, 'message' => 'Logout successfully']);
        } catch (\Throwable $e) {
            Log::error('AuthController::logout error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    // ME
    public function me(Request $request)
    {
        try {
            $user = $this->authService->me($request);
            return response()->json(['success' => true, 'data' => $user]);
        } catch (\Throwable $e) {
            Log::error('AuthController::me error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
}
