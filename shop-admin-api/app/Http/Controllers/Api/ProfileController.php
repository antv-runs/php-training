<?php

namespace App\Http\Controllers\Api;

use App\Contracts\ProfileServiceInterface;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\UserResource;

class ProfileController extends BaseController
{
    private ProfileServiceInterface $profileService;

    public function __construct(ProfileServiceInterface $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Return the authenticated user's data
     */
    public function edit()
    {
        $user = auth()->user();
        return $this->success(new UserResource($user), 'Edit profile');
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(ProfileRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image');
        }

        $user = $this->profileService->updateProfile($user, $validated);

        return $this->success(new UserResource($user), 'Profile updated successfully');
    }

    /**
     * Delete the authenticated user's profile image.
     */
    public function deleteImage()
    {
        $user = auth()->user();
        $user = $this->profileService->deleteProfileImage($user);

        return $this->success(new UserResource($user), 'Profile image deleted successfully');
    }
}
