<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Http\Request;

interface UserServiceInterface
{
    /**
     * Build query with search and filters
     */
    public function buildQuery(Request $request);

    /**
     * Get paginated users data with metadata
     */
    public function getListData(Request $request);

    /**
     * Get role options
     */
    public function getRoles();

    /**
     * Create a new user
     */
    public function createUser(array $data);

    /**
     * Update user
     */
    public function updateUser(User $user, array $data);

    /**
     * Delete user (soft delete)
     */
    public function deleteUser(User $user);

    /**
     * Get trashed users
     */
    public function getTrashed(Request $request);

    /**
     * Restore user
     */
    public function restoreUser($id);

    /**
     * Force delete user
     */
    public function forceDeleteUser($id);
}
