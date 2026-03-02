<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Contracts\AuthServiceInterface;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseController
{
    private AuthServiceInterface $authService;

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
        $result = $this->authService->register($request->only(['name', 'email', 'password']));
        return $this->success($result, 'Register successfully', Response::HTTP_CREATED);
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
     *                 @OA\Property(property="email", type="string", format="email", example="uter.vanan@gmail.com"),
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
        $credentials = $request->validated();
        $result = $this->authService->login($credentials);
        return $this->success($result, 'Login successfully');
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
        $this->authService->logout($request);
        return $this->success(null, 'Logout successfully');
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
        $user = $this->authService->me($request);
        return $this->success(new UserResource($user), 'User info retrieved');
    }
}
