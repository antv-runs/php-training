<?php

namespace App\Http\Controllers;

use App\Contracts\ProfileServiceInterface;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * @var ProfileServiceInterface
     */
    private $profileService;

    /**
     * Inject ProfileServiceInterface
     */
    public function __construct(ProfileServiceInterface $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Get the authenticated user's profile.
     * Returns JSON response.
     */
    public function show()
    {
        $user = auth()->user();
        return response()->json([
            'message' => 'Profile retrieved successfully',
            'user' => $user
        ]);
    }

    /**
     * Update the user's profile information.
     * Returns JSON response.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $this->profileService->validateProfileData(
            $request->all(),
            $user->id
        );

        // Handle image file
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image');
        } else {
            unset($validated['profile_image']);
        }

        $result = $this->profileService->updateProfile($user, $validated);

        return response()->json([
            'message' => $result['message'],
            'user' => $result['data'] ?? $user
        ]);
    }

    /**
     * Delete the user's profile image.
     * Returns JSON response.
     */
    public function deleteImage()
    {
        $user = auth()->user();
        $result = $this->profileService->deleteProfileImage($user);

        return response()->json([
            'message' => $result['message'],
            'user' => $result['data'] ?? $user
        ]);
    }
}
