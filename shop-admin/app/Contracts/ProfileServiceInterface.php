<?php

namespace App\Contracts;

interface ProfileServiceInterface
{
    /**
     * Update user profile
     */
    public function updateProfile($user, array $data);

    /**
     * Delete user's profile image
     */
    public function deleteProfileImage($user);

    /**
     * Validate profile data
     */
    public function validateProfileData(array $data, $userId);
}
