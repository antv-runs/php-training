<?php

namespace App\Services;

use App\Contracts\ProfileServiceInterface;
use Illuminate\Support\Facades\Storage;

class ProfileService implements ProfileServiceInterface
{
    /**
     * Update user profile
     */
    public function updateProfile($user, array $data)
    {
        // Handle image upload
        if (isset($data['profile_image'])) {
            // Delete old image if exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Store new image
            $path = $data['profile_image']->store('profile-images', 'public');
            $data['profile_image'] = $path;
        }

        $user->update($data);

        return [
            'success' => true,
            'message' => 'Profile updated successfully!',
            'data' => $user
        ];
    }

    /**
     * Delete user's profile image
     */
    public function deleteProfileImage($user)
    {
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->update(['profile_image' => null]);

        return [
            'success' => true,
            'message' => 'Profile image deleted successfully!'
        ];
    }

    /**
     * Validate profile data
     */
    public function validateProfileData(array $data, $userId)
    {
        return validator($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'bio' => ['nullable', 'string', 'max:500'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ])->validated();
    }
}
