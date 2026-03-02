<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\UserRequest;
use App\Contracts\UserServiceInterface;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseController
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * Inject UserServiceInterface
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get user list",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User list retrieved successfully"
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(Request $request)
    {
        $data = $this->userService->getListData($request);
        return $this->success($data, 'User list retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create new user",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="12345678"),
     *             @OA\Property(property="is_admin", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created successfully"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $user = $this->userService->createUser($data);

        return $this->success(new UserResource($user), 'User created successfully', Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get user detail",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="User retrieved successfully"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show($id)
    {
        $user = $this->userService->getUser($id);
        return $this->success(new UserResource($user), 'User retrieved successfully');
    }

    /**
     * @OA\Patch(
     *     path="/api/users/{id}",
     *     summary="Update user",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Name"),
     *             @OA\Property(property="email", type="string", example="updated@example.com"),
     *             @OA\Property(property="password", type="string", example="newpassword"),
     *             @OA\Property(property="is_admin", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated successfully"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(UserRequest $request, $id)
    {
        $data = $request->validated();
        $user = $this->userService->updateUser($id, $data);
        return $this->success(new UserResource($user), 'User updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Soft delete user",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="User deleted successfully"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function destroy($id)
    {
        $this->userService->deleteUser($id);
        return $this->success(null, 'User deleted successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/users/trashed",
     *     summary="Get soft deleted users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Trashed users retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function trashed(Request $request)
    {
        $data = $this->userService->getTrashed($request);
        return $this->success($data, 'Trashed users retrieved successfully');
    }

    /**
     * @OA\Patch(
     *     path="/api/users/{id}/restore",
     *     summary="Restore user",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="User restored successfully"),
     *     @OA\Response(response=400, description="Restore failed"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function restore($id)
    {
        $user = $this->userService->restoreUser($id);
        return $this->success(new UserResource($user), 'User restored successfully');
    }

    /**
    * @OA\Delete(
    *     path="/api/users/{id}/force-delete",
    *     summary="Permanently delete user",
    *     tags={"Users"},
    *     security={{"bearerAuth":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Response(response=200, description="User permanently deleted"),
    *     @OA\Response(response=401, description="Unauthenticated"),
    *     @OA\Response(response=403, description="Forbidden")
    * )
    */
    public function forceDelete($id)
    {
        $this->userService->forceDeleteUser($id);
        return $this->success(null, 'User permanently deleted');
    }
}

