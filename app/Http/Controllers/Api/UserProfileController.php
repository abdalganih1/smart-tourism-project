<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Import User model
use App\Models\UserProfile; // Import UserProfile model
use App\Models\UserPhoneNumber; // Import UserPhoneNumber model
use App\Http\Requests\Api\UpdateProfileRequest; // Import custom Update Request
use App\Http\Resources\UserResource; // Import User Resource (to return User with profile)
use Illuminate\Support\Facades\Auth; // For authentication
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Storage; // For file storage
use Illuminate\Support\Facades\DB; // For database transactions


class UserProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     * Accessible at GET /api/profile
     * Requires authentication.
     *
     * @return \App\Http\Resources\Json\JsonResource
     */
    public function show()
    {
        // Authentication check handled by 'auth:sanctum' middleware

        // Get the authenticated user
        $user = Auth::user();

        // Load the profile and phone numbers relationships
        $user->load(['profile', 'phoneNumbers']);

        // Return the User resource (which includes the profile)
        return new UserResource($user);
    }

    /**
     * Update the authenticated user's profile.
     * Accessible at PUT/PATCH /api/profile
     * Requires authentication.
     *
     * @param  \App\Http\Requests\Api\UpdateProfileRequest  $request // Use custom request for validation
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateProfileRequest $request)
    {
         // Validation is handled by UpdateProfileRequest
         // Authentication check handled by 'auth:sanctum' middleware
         // Authorization (user is updating their own profile) handled by UpdateProfileRequest authorize() method

        $user = Auth::user(); // Get the authenticated user

        DB::beginTransaction();
        $oldProfilePhotoUrl = $user->profile->profile_picture_url ?? null; // Store old path


        try {
            // Update User Profile details
            // Use updateOrCreate if profile might not exist (though it should after registration)
            $profileData = $request->only([
                'first_name', 'last_name', 'father_name', 'mother_name', 'bio'
            ]);

             // Handle profile picture upload
             if ($request->hasFile('profile_picture')) {
                 $photoPath = $request->file('profile_picture')->store('profile_pictures', 'public');
                 $profileData['profile_picture_url'] = '/storage/' . $photoPath;

                 // Delete old photo if it existed
                 if ($oldProfilePhotoUrl && Storage::disk('public')->exists(str_replace('/storage/', '', $oldProfilePhotoUrl))) {
                      Storage::disk('public')->delete(str_replace('/storage/', '', $oldProfilePhotoUrl));
                 }
             } else if ($request->boolean('remove_profile_picture')) { // Handle checkbox to remove photo
                  $profileData['profile_picture_url'] = null;
                  if ($oldProfilePhotoUrl && Storage::disk('public')->exists(str_replace('/storage/', '', $oldProfilePhotoUrl))) {
                      Storage::disk('public')->delete(str_replace('/storage/', '', $oldProfilePhotoUrl));
                  }
             }


             $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);


             // --- Handle Phone Numbers Update (More complex) ---
             // Requires a specific structure in the request (e.g., array of phones with IDs and a flag for deletion)
             // This is a placeholder for the logic.
             /*
             if ($request->has('phone_numbers')) {
                 $providedPhoneNumbers = $request->input('phone_numbers'); // Array of phones from request

                 // Get existing phone numbers
                 $existingPhoneNumbers = $user->phoneNumbers;

                 // Example logic: Sync phone numbers - Delete existing, create provided ones.
                 // This is simple but destructive if user only wants to update one.
                 // A more robust approach: Iterate through provided, update/create based on ID,
                 // identify IDs in existing but not provided and delete them.
                 $user->phoneNumbers()->delete();
                 foreach ($providedPhoneNumbers as $phoneData) {
                      if (isset($phoneData['phone_number'])) {
                           $user->phoneNumbers()->create([
                               'phone_number' => $phoneData['phone_number'],
                               'is_primary' => $phoneData['is_primary'] ?? false,
                               'description' => $phoneData['description'] ?? null,
                           ]);
                      }
                 }
             }
              */

             // Optional: Update User fields directly (username, email, user_type, etc.) if allowed via profile update
             // $user->update($request->only(['username', 'email']));


            DB::commit(); // Commit transaction

            // Reload relationships after update
             $user->load(['profile', 'phoneNumbers']);

            // Return the updated User resource
            return new UserResource($user);

        } catch (\Exception $e) {
             DB::rollBack(); // Rollback transaction
             // Clean up uploaded file if transaction failed
             if (isset($photoPath)) {
                  Storage::disk('public')->delete($photoPath);
             }
             Log::error('Error updating user profile: ' . $e->getMessage(), ['user_id' => $user->id, 'request_data' => $request->all(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to update profile. Please try again.'], 500);
        }
    }
     /**
      * Display the specified resource. (Not used for UserProfile)
      */
     // public function show(UserProfile $userProfile) { ... } // Handled by User show

     /**
      * Store a newly created resource in storage. (Not used for UserProfile)
      */
     // public function store(Request $request) { ... } // Handled during User registration

     /**
      * Remove the specified resource from storage. (Not used for UserProfile - deleting user cascades)
      */
     // public function destroy(UserProfile $userProfile) { ... }
}