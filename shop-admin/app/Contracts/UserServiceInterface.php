<?php

namespace App\Contracts;

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
    public function updateUser($user, array $data);

    /**
     * Delete user
     */
    public function deleteUser($user);
}
