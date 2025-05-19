<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteExperience; // Import SiteExperience model
use App\Models\TouristSite; // Import TouristSite model (for dropdown/validation)
use App\Http\Requests\Api\StoreSiteExperienceRequest; // Import custom Store Request
use App\Http\Requests\Api\UpdateSiteExperienceRequest; // Import custom Update Request
use App\Http\Resources\SiteExperienceResource; // Import SiteExperience Resource
use Illuminate\Support\Facades\Auth; // For authentication
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Auth\Access\AuthorizationException; // For authorization errors
use Illuminate\Support\Facades\Storage; // For file storage
use Illuminate\Support\Facades\DB; // For database transactions


class SiteExperienceController extends Controller
{
    /**
     * Display a listing of the authenticated user's site experiences.
     * Accessible at GET /api/my-experiences (Custom route name suggested)
     * Requires authentication.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // Authentication check handled by 'auth:sanctum' middleware

        // Fetch the authenticated user's site experiences
        // Eager load the site relationship
        $siteExperiences = Auth::user()->siteExperiences()->with('site:id,name')->orderBy('created_at', 'desc')->paginate(15); // Adjust pagination/ordering

        // Return the collection using SiteExperienceResource
        return SiteExperienceResource::collection($siteExperiences);
    }

    /**
     * Store a new site experience for the authenticated user.
     * Accessible at POST /api/site-experiences
     * Requires authentication.
     *
     * @param  \App\Http\Requests\Api\StoreSiteExperienceRequest  $request // Use custom request for validation
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\SiteExperienceResource
     */
    public function store(StoreSiteExperienceRequest $request)
    {
        // Validation is handled by StoreSiteExperienceRequest (includes site existence check)
        // Authentication check handled by 'auth:sanctum' middleware

        $userId = Auth::id();

        DB::beginTransaction();

        try {
            $experienceData = $request->only(['site_id', 'title', 'content', 'visit_date']);
            $experienceData['user_id'] = $userId; // Assign the authenticated user

            // Handle file upload for photo if present
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('experience_photos', 'public'); // Store in 'storage/app/public/experience_photos'
                $experienceData['photo_url'] = '/storage/' . $photoPath; // Save public path
            } else {
                $experienceData['photo_url'] = null; // Ensure null if no file uploaded
            }


            // Create the SiteExperience
            $siteExperience = SiteExperience::create($experienceData);

            DB::commit(); // Commit transaction

            // Load relationship needed for the resource response
            $siteExperience->load(['user.profile', 'site']);

            // Return the created experience using SiteExperienceResource
            return new SiteExperienceResource($siteExperience);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            // Clean up uploaded file if transaction failed
            if (isset($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            Log::error('Error creating site experience: ' . $e->getMessage(), ['user_id' => $userId, 'request_data' => $request->all(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to create site experience. Please try again.'], 500);
        }
    }

    /**
     * Display a specific site experience for the authenticated user.
     * Accessible at GET /api/site-experiences/{siteExperience}
     * Requires authentication and authorization (user owns the experience).
     *
     * @param  \App\Models\SiteExperience  $siteExperience // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function show(SiteExperience $siteExperience)
    {
        // Authentication check handled by middleware

        // Authorization: Ensure the authenticated user owns this experience
        if ($siteExperience->user_id !== Auth::id()) {
             throw new AuthorizationException('You do not own this site experience.');
        }

        // Load relationships needed for the detailed view
        $siteExperience->load(['user.profile', 'site']); // Load user with profile, and site

        // Return the single experience using SiteExperienceResource
        return new SiteExperienceResource($siteExperience);
    }

    /**
     * Update a specific site experience for the authenticated user.
     * Accessible at PUT/PATCH /api/site-experiences/{siteExperience}
     * Requires authentication and authorization (user owns the experience).
     *
     * @param  \App\Http\Requests\Api\UpdateSiteExperienceRequest  $request // Use custom request for validation
     * @param  \App\Models\SiteExperience  $siteExperience // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateSiteExperienceRequest $request, SiteExperience $siteExperience)
    {
         // Validation is handled by UpdateSiteExperienceRequest
         // Authentication check handled by middleware
         // Authorization (user owns experience) check handled by UpdateSiteExperienceRequest authorize() method

        DB::beginTransaction();
        $oldPhotoUrl = $siteExperience->photo_url; // Store old photo path for potential deletion


        try {
            $experienceData = $request->only(['site_id', 'title', 'content', 'visit_date']);

             // Handle file upload for photo if present
            if ($request->hasFile('photo')) {
                // Upload the new photo
                $photoPath = $request->file('photo')->store('experience_photos', 'public');
                $experienceData['photo_url'] = '/storage/' . $photoPath; // Save public path

                 // Delete the old photo file if it existed
                 if ($oldPhotoUrl && Storage::disk('public')->exists(str_replace('/storage/', '', $oldPhotoUrl))) {
                      Storage::disk('public')->delete(str_replace('/storage/', '', $oldPhotoUrl));
                 }
            } else if ($request->boolean('remove_photo')) { // Handle checkbox to remove photo
                 $experienceData['photo_url'] = null;
                 if ($oldPhotoUrl && Storage::disk('public')->exists(str_replace('/storage/', '', $oldPhotoUrl))) {
                     Storage::disk('public')->delete(str_replace('/storage/', '', $oldPhotoUrl));
                 }
            }


            // Update the SiteExperience
            $siteExperience->update($experienceData);

            DB::commit(); // Commit transaction

            // Load relationship needed for the resource response
             $siteExperience->load(['user.profile', 'site']); // Reload relationships after update

            // Return the updated experience using SiteExperienceResource
            return new SiteExperienceResource($siteExperience);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            // If a new file was uploaded, clean it up on error
            if (isset($photoPath)) {
                 Storage::disk('public')->delete($photoPath);
            }
             Log::error('Error updating site experience: ' . $e->getMessage(), ['experience_id' => $siteExperience->id, 'user_id' => Auth::id(), 'request_data' => $request->all(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to update site experience. Please try again.'], 500);
        }
    }

    /**
     * Remove a specific site experience for the authenticated user.
     * Accessible at DELETE /api/site-experiences/{siteExperience}
     * Requires authentication and authorization (user owns the experience).
     *
     * @param  \App\Models\SiteExperience  $siteExperience // Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(SiteExperience $siteExperience)
    {
        // Authentication check handled by middleware
        // Authorization (user owns experience) check can be done here explicitly
        if ($siteExperience->user_id !== Auth::id()) {
             throw new AuthorizationException('You do not own this site experience.');
        }

        DB::beginTransaction();
        $oldPhotoUrl = $siteExperience->photo_url; // Store old photo path to delete

        try {
             // Deleting an experience should cascade delete related polymorphic data
             // if onDelete('cascade') is set up in migrations for Comments/Ratings/Favorites
             // where this experience is the target.


             $siteExperience->delete(); // This attempts deletion. If using soft deletes, it marks as deleted.

             DB::commit(); // Commit transaction

             // Delete the image file after successful DB deletion
             if ($oldPhotoUrl && Storage::disk('public')->exists(str_replace('/storage/', '', $oldPhotoUrl))) {
                  Storage::disk('public')->delete(str_replace('/storage/', '', $oldPhotoUrl));
             }


            // Return a success response
            return response()->json(['message' => 'Site experience deleted successfully.'], 200); // Using 200 with message

        } catch (\Exception $e) {
             DB::rollBack(); // Rollback transaction
             Log::error('Error deleting site experience: ' . $e->getMessage(), ['experience_id' => $siteExperience->id, 'user_id' => Auth::id(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to delete site experience. Please try again.'], 500);
        }
    }

    // --- Optional Methods for Polymorphic Relationships ---
    // (Similar methods to CommentController/RatingController for fetching comments/ratings/favorites targeting this experience)
    // Example: get comments for this experience:
    // public function comments(SiteExperience $siteExperience) { ... }
}