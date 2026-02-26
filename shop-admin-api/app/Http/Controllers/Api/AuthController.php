<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Contracts\AuthServiceInterface;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    // Register
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="User registration",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="Nguyen Van A"),
     *                 @OA\Property(property="email", type="string", example="vana@example.com"),
     *                 @OA\Property(property="password", type="string", example="12345678"),
     *                 @OA\Property(property="password_confirmation", type="string", example="12345678")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(RegisterRequest $request)
    {
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

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="User login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *                 @OA\Property(property="password", type="string", format="password", example="password")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();;
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

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout current user",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Logout successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logout successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Not authenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Get current authenticated user",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User info",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="An Van"),
     *                 @OA\Property(property="email", type="string", example="an@example.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function me(Request $request)
    {
        try {
            $user = $this->authService->me($request);
            Log::info('AuthController::me user: '.($user ? $user->email : 'null'));
            return response()->json(['success' => true, 'data' => $user]);
        } catch (\Throwable $e) {
            Log::error('AuthController::me error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
}
