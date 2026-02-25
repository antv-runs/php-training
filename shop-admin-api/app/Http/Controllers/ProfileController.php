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
     * Show the user's profile.
     */
    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
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

        return redirect()->route('profile.show')->with('success', $result['message']);
    }

    /**
     * Delete the user's profile image.
     */
    public function deleteImage()
    {
        $user = auth()->user();
        $result = $this->profileService->deleteProfileImage($user);

        return back()->with('success', $result['message']);
    }
}
