<?php

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\ItemStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserService implements UserServiceInterface
{
    /**
     * Build query with search and filters
     */
    public function buildQuery(Request $request)
    {
        $status = $request->input('status', ItemStatus::ACTIVE->value);

        // Query builder based on status
        if ($status === ItemStatus::DELETED->value) {
            $query = User::onlyTrashed();
        } elseif ($status === ItemStatus::ALL->value) {
            $query = User::withTrashed();
        } else {
            $query = User::query();
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role') && $status !== ItemStatus::DELETED->value) {
            $query->where('role', $request->input('role'));
        }

        // Sort
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');

        if (in_array($sortBy, ['id', 'name', 'email', 'role', 'created_at', 'deleted_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }

    /**
     * Get paginated users data with metadata
     */
    public function getListData(Request $request)
    {
        $perPage = (int)$request->input('per_page', 15);
        $users = $this->buildQuery($request)->paginate($perPage);

        return [
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
            'filters' => [
                'search' => $request->input('search'),
                'status' => $request->input('status', ItemStatus::ACTIVE->value),
                'role' => $request->input('role'),
                'sort_by' => $request->input('sort_by', 'id'),
                'sort_order' => $request->input('sort_order', 'desc'),
            ],
            'paginator' => $users
        ];
    }

    /**
     * Get role options
     */
    public function getRoles()
    {
        return UserRole::options();
    }

    /**
     * Create a new user
     */
    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    /**
     * Update user
     */
    public function updateUser(User $user, array $data)
    {
        // Prevent admin from removing their own admin role
        if (Auth::id() === $user->id && ($data['role'] ?? $user->role) !== UserRole::ADMIN->value) {
            return [
                'success' => false,
                'message' => 'You cannot remove your own admin role.'
            ];
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return [
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ];
    }

    /**
     * Delete user (soft delete)
     */
    public function deleteUser(User $user)
    {
        // Prevent deleting yourself
        if (Auth::id() === $user->id) {
            return [
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ];
        }

        $user->delete();

        return [
            'success' => true,
            'message' => 'User deleted successfully'
        ];
    }

    /**
     * Get trashed users
     */
    public function getTrashed(\Illuminate\Http\Request $request)
    {
        $perPage = (int)$request->input('per_page', 15);
        $query = User::onlyTrashed();

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest('deleted_at')->paginate($perPage);

        return [
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
            'paginator' => $users
        ];
    }

    /**
     * Restore user
     */
    public function restoreUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if (!$user->trashed()) {
            return [
                'success' => false,
                'message' => 'User is not deleted.'
            ];
        }

        $user->restore();

        return [
            'success' => true,
            'message' => 'User restored successfully',
            'data' => $user
        ];
    }

    /**
     * Force delete user
     */
    public function forceDeleteUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return [
            'success' => true,
            'message' => 'User permanently deleted'
        ];
    }
}
