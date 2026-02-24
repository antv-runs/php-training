<?php

namespace App\Contracts;

use App\Models\User;

interface ProfileServiceInterface
{
    /**
     * Update user profile
     */
    public function updateProfile(User $user, array $data);

    /**
     * Delete user's profile image
     */
    public function deleteProfileImage($user);

    /**
     * Validate profile data
     */
    public function validateProfileData(array $data, $userId);
}
