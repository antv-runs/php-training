<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
     * Show the admin's profile.
     */
    public function show()
    {
        $user = auth()->user();
        return view('admin.profile.show', compact('user'));
    }

    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the admin's profile information.
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

        return redirect()->route('admin.profile.show')->with('success', $result['message']);
    }

    /**
     * Delete the admin's profile image.
     */
    public function deleteImage()
    {
        $user = auth()->user();
        $result = $this->profileService->deleteProfileImage($user);

        return back()->with('success', $result['message']);
    }
}
