<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteExperience; // Import SiteExperience model
use App\Models\User; // Import User model (for user relationship)
use App\Models\TouristSite; // Import TouristSite model (for site relationship)
// No need for Store/Update requests for admin panel if only showing/deleting user content.
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // For authorization check (optional, handled by middleware/policy)
use Illuminate\Support\Facades\Storage; // For file storage
use DB;

class SiteExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/site-experiences
     * Optional: Add filters (by user, site, dates, keywords)
     */
    public function index(Request $request)
    {
        // Fetch site experiences with user and site relationships, paginated
        $query = SiteExperience::with(['user:id,username', 'site:id,name']); // Load user and site

         // Optional: Add filtering logic here if needed
         // Example: Filter by user_id
         if ($request->filled('user_id')) {
             $query->where('user_id', $request->user_id);
         }
         // Example: Filter by site_id
         if ($request->filled('site_id')) {
             $query->where('site_id', $request->site_id);
         }
         // ... add other filters


        $siteExperiences = $query->orderBy('created_at', 'desc') // Order by creation date
                                 ->paginate(15); // Paginate results

        // Optional: Pass data for filter dropdowns
         $users = User::select('id', 'username')->orderBy('username')->get();
         $sites = TouristSite::select('id', 'name')->orderBy('name')->get();


        // Return the view and pass the data
        return view('admin.site_experiences.index', compact('siteExperiences', 'users', 'sites'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/site-experiences/create
     * Note: Admins typically do not create user experiences. Redirect or show error.
     */
    public function create()
    {
        // Redirect back to index with a message
        return redirect()->route('admin.site-experiences.index')->with('info', 'Admin users cannot directly create new site experiences. Experiences are created by tourists.');

        // // Or return an error view
        // abort(403, 'Admin cannot create site experiences.');
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/site-experiences
     * Note: Admins typically do not store user experiences. Redirect or show error.
     */
    public function store(Request $request)
    {
         // Redirect back to index with a message
         return redirect()->route('admin.site-experiences.index')->with('info', 'Admin users cannot directly create new site experiences. Experiences are created by tourists.');
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/site-experiences/{siteExperience}
     * This is the primary view for moderation.
     *
     * @param  \App\Models\SiteExperience  $siteExperience // Route Model Binding
     */
    public function show(SiteExperience $siteExperience)
    {
        // Load relationships for the detailed view
        $siteExperience->load(['user.profile', 'site']); // Load user with profile, and site

        // Return the view and pass the data
        return view('admin.site_experiences.show', compact('siteExperience'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/site-experiences/{siteExperience}/edit
     * Note: Admins typically do not edit user experiences. Redirect or show error.
     */
    public function edit(SiteExperience $siteExperience)
    {
        // Redirect back to show page with a message
        return redirect()->route('admin.site-experiences.show', $siteExperience)->with('info', 'Admin users cannot directly edit site experiences. You can only view or delete them.');
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/site-experiences/{siteExperience}
     * Note: Admins typically do not update user experiences. Redirect or show error.
     */
    public function update(Request $request, SiteExperience $siteExperience)
    {
         // Redirect back to show page with a message
         return redirect()->route('admin.site-experiences.show', $siteExperience)->with('info', 'Admin users cannot directly update site experiences. You can only view or delete them.');

         // // Optional: If you implement moderation status update (e.g., approve/reject),
         // // you would define a custom route/method for that, not use the standard update.
         // // Example:
         // // if ($request->has('moderation_status')) {
         // //    $siteExperience->update(['moderation_status' => $request->moderation_status]);
         // //    return redirect()->route('admin.site-experiences.index')->with('success', 'Experience status updated.');
         // // }
         // // return redirect()->route('admin.site-experiences.show', $siteExperience)->with('error', 'Invalid update request.');
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/site-experiences/{siteExperience}
     * This is a common action for moderation.
     *
     * @param  \App\Models\SiteExperience  $siteExperience // Route Model Binding
     */
    public function destroy(SiteExperience $siteExperience)
    {
         DB::beginTransaction();
         $oldPhotoUrl = $siteExperience->photo_url; // Store old photo path to delete

         try {
             // Optional: Check for related polymorphic data (Comments, Ratings, Favorites pointing to this experience)
             // If delete cascade is set up, these should be removed automatically.

             $siteExperience->delete(); // This attempts deletion. If using soft deletes, it marks as deleted.

             DB::commit(); // Commit transaction

             // Delete the image file after successful DB deletion
             if ($oldPhotoUrl && Storage::disk('public')->exists(str_replace('/storage/', '', $oldPhotoUrl))) {
                  Storage::disk('public')->delete(str_replace('/storage/', '', $oldPhotoUrl));
             }

             return redirect()->route('admin.site-experiences.index')->with('success', 'Site experience deleted successfully!');

         } catch (\Exception $e) {
             DB::rollBack(); // Rollback transaction
             Log::error('Error deleting site experience: ' . $e->getMessage(), ['experience_id' => $siteExperience->id, 'exception' => $e]);
              // Catch specific exceptions if needed
             return redirect()->route('admin.site-experiences.index')->with('error', 'Error deleting site experience: ' . $e->getMessage());
         }
    }
}