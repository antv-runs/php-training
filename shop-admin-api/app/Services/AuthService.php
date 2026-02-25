<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Contracts\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{
    public function register(array $data): array
    {
        Log::info('AuthService: register called', ['email' => $data['email'] ?? null]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        Log::info('AuthService: register successful', ['user_id' => $user->id]);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials): array
    {
        Log::info('AuthService: login attempt', ['email' => $credentials['email'] ?? null]);

        if (!Auth::attempt($credentials)) {
            Log::warning('AuthService: login failed', ['email' => $credentials['email'] ?? null]);
            return [];
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        Log::info('AuthService: login successful', ['user_id' => $user->id]);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(Request $request): bool
    {
        $user = $request->user();
        if (! $user) {
            Log::warning('AuthService: logout called with no authenticated user');
            return false;
        }

        $request->user()->currentAccessToken()->delete();
        Log::info('AuthService: logout successful', ['user_id' => $user->id]);
        return true;
    }

    public function me(Request $request)
    {
        Log::info('AuthService: me called', ['user_id' => optional($request->user())->id]);
        return $request->user();
    }
}
