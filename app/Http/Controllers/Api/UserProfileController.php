<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPhoneNumber;
use App\Http\Requests\Api\UpdateProfileRequest; // For updating textual profile info
use App\Http\Requests\Api\UpdatePasswordRequest; // New request for password update
use App\Http\Requests\Api\UpdateProfilePictureRequest; // New request for picture upload
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function show()
    {
        $user = Auth::user()->load(['profile', 'phoneNumbers']);
        return new UserResource($user);
    }

    /**
     * Update the authenticated user's profile information (textual data).
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        try {
            // Update UserProfile details
            $profileData = $request->only([
                'first_name', 'last_name', 'father_name', 'mother_name', 'bio'
            ]);

            // updateOrCreate ensures a profile is created if it somehow doesn't exist
            $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

            // Reload the profile to get the updated data
            $user->load('profile');

            return new UserResource($user);

        } catch (\Exception $e) {
            Log::error('Error updating user profile text data: ' . $e->getMessage(), ['user_id' => $user->id, 'exception' => $e]);
            return response()->json(['message' => 'Failed to update profile. Please try again.'], 500);
        }
    }

    /**
     * Update the authenticated user's profile picture.
     */
    public function updateProfilePicture(UpdateProfilePictureRequest $request)
    {
        $user = Auth::user();
        $oldProfilePhotoUrl = $user->profile->profile_picture_url ?? null;

        try {
            // Store the new photo
            $photoPath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $profileData['profile_picture_url'] = '/storage/' . $photoPath;

            // Update the profile with the new photo URL
            $user->profile()->update($profileData);

            // Delete the old photo if it existed and is not the default
            if ($oldProfilePhotoUrl && Storage::disk('public')->exists(str_replace('/storage/', '', $oldProfilePhotoUrl))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $oldProfilePhotoUrl));
            }

            return response()->json([
                'message' => 'Profile picture updated successfully.',
                'profile_picture_url' => $profileData['profile_picture_url']
            ]);

        } catch (\Exception $e) {
             Log::error('Error updating profile picture: ' . $e->getMessage(), ['user_id' => $user->id, 'exception' => $e]);
             return response()->json(['message' => 'Failed to update profile picture.'], 500);
        }
    }

    /**
     * Remove the authenticated user's profile picture.
     */
    public function removeProfilePicture()
    {
        $user = Auth::user();
        $oldProfilePhotoUrl = $user->profile->profile_picture_url ?? null;

        if (!$oldProfilePhotoUrl) {
            return response()->json(['message' => 'No profile picture to remove.'], 404);
        }

        try {
            // Delete the old photo from storage
            if (Storage::disk('public')->exists(str_replace('/storage/', '', $oldProfilePhotoUrl))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $oldProfilePhotoUrl));
            }

            // Set the profile picture URL to null in the database
            $user->profile()->update(['profile_picture_url' => null]);

            return response()->json(['message' => 'Profile picture removed successfully.'], 200);

        } catch (\Exception $e) {
            Log::error('Error removing profile picture: ' . $e->getMessage(), ['user_id' => $user->id, 'exception' => $e]);
            return response()->json(['message' => 'Failed to remove profile picture.'], 500);
        }
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        // Validation is handled by UpdatePasswordRequest
        // The request should have already checked if current_password matches

        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Optional: Logout user from all other devices for security
            // Auth::logoutOtherDevices($request->password);

            return response()->json(['message' => 'Password updated successfully.']);

        } catch (\Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage(), ['user_id' => $user->id, 'exception' => $e]);
            return response()->json(['message' => 'Failed to update password.'], 500);
        }
    }
}