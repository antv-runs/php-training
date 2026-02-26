<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Contracts\UserServiceInterface;
use Illuminate\Http\Request;

class UserController extends Controller
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
     * Get list of users with pagination.
     * Returns JSON response.
     */
    public function index(Request $request)
    {
        $data = $this->userService->getListData($request);
        return response()->json($data);
    }

    /**
     * Store a newly created user.
     * Returns JSON response.
     */
    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $result = $this->userService->createUser($data);

        if (!($result['success'] ?? true)) {
            return response()->json(['message' => $result['message']], 400);
        }

        return response()->json([
            'message' => 'User created successfully',
            'data' => $result['data'] ?? null
        ], 201);
    }

    /**
     * Get a specific user by ID.
     * Returns JSON response.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $user
        ]);
    }

    /**
     * Update a specific user.
     * Returns JSON response.
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validated();

        $result = $this->userService->updateUser($user, $data);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 403);
        }

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data']
        ]);
    }

    /**
     * Soft delete a user.
     * Returns JSON response.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $result = $this->userService->deleteUser($user);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 403);
        }

        return response()->json(['message' => $result['message']]);
    }

    /**
     * Get trashed (soft deleted) users.
     * Returns JSON response.
     */
    public function trashed(Request $request)
    {
        $data = $this->userService->getTrashed($request);
        return response()->json($data);
    }

    /**
     * Restore a soft deleted user.
     * Returns JSON response.
     */
    public function restore($id)
    {
        $result = $this->userService->restoreUser($id);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 400);
        }

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data']
        ]);
    }

    /**
     * Force delete a user permanently.
     * Returns JSON response.
     */
    public function forceDelete($id)
    {
        $result = $this->userService->forceDeleteUser($id);

        return response()->json(['message' => $result['message']]);
    }
}

