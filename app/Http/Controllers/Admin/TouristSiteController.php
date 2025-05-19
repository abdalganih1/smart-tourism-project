<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TouristSite; // Import TouristSite model
use App\Models\SiteCategory; // Import SiteCategory model
use App\Models\User; // Import User model (for added_by_user_id)
use App\Http\Requests\Admin\StoreTouristSiteRequest; // Import custom Store Request
use App\Http\Requests\Admin\UpdateTouristSiteRequest; // Import custom Update Request
use Illuminate\Support\Facades\Storage; // For file storage
use Illuminate\Support\Facades\DB; // For database transactions
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // To check authenticated user (optional, handled by middleware/policy)


class TouristSiteController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/tourist-sites
     */
    public function index()
    {
        // Fetch tourist sites with their category and addedBy relationships, paginated
        $touristSites = TouristSite::with(['category', 'addedBy:id,username']) // Load category and simplified addedBy user
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(10); // Paginate results

        // Return the view and pass the data
        return view('admin.tourist_sites.index', compact('touristSites'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/tourist-sites/create
     */
    public function create()
    {
        // Fetch necessary data for the form
        $categories = SiteCategory::orderBy('name')->get();
        // Fetch users who can add sites (Admin, Employee). Pass only ID and username.
        $siteManagers = User::whereIn('user_type', ['Admin', 'Employee'])->select('id', 'username')->get();

        // Return the view and pass data
        return view('admin.tourist_sites.create', compact('categories', 'siteManagers'));
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/tourist-sites
     *
     * @param  \App\Http\Requests\Admin\StoreTouristSiteRequest  $request // Use custom request for validation
     */
    public function store(StoreTouristSiteRequest $request)
    {
        // Validation is handled by StoreTouristSiteRequest

        DB::beginTransaction();

        try {
            $siteData = $request->only([
                'name', 'description', 'location_text', 'latitude',
                'longitude', 'city', 'country', 'category_id', 'video_url',
                'added_by_user_id'
            ]);

            // Handle file upload for main_image if present
            if ($request->hasFile('main_image')) {
                $imagePath = $request->file('main_image')->store('tourist_sites', 'public'); // Store in 'storage/app/public/tourist_sites'
                $siteData['main_image_url'] = '/storage/' . $imagePath; // Save public path
            }

            // Set country default if not provided and column is nullable (though schema has default 'Syria')
             if (!isset($siteData['country'])) {
                 $siteData['country'] = 'Syria';
             }


            // Create the TouristSite
            $touristSite = TouristSite::create($siteData);

            DB::commit(); // Commit transaction

            return redirect()->route('admin.tourist-sites.index')->with('success', 'Tourist Site created successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            // Clean up uploaded file if transaction failed and file was stored
            if (isset($imagePath)) {
                 Storage::disk('public')->delete($imagePath);
            }
            Log::error('Error creating tourist site: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error creating tourist site: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/tourist-sites/{touristSite}
     *
     * @param  \App\Models\TouristSite  $touristSite // Route Model Binding
     */
    public function show(TouristSite $touristSite)
    {
        // Load relationships for the detailed view
        $touristSite->load(['category', 'addedBy.profile']); // Load category and addedBy user with profile

        // Return the view and pass the data
        return view('admin.tourist_sites.show', compact('touristSite'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/tourist-sites/{touristSite}/edit
     *
     * @param  \App\Models\TouristSite  $touristSite // Route Model Binding
     */
    public function edit(TouristSite $touristSite)
    {
        // Load relationships for the edit form
        $touristSite->load(['category', 'addedBy.profile']);

        // Fetch necessary data for the form (same as create)
        $categories = SiteCategory::orderBy('name')->get();
        $siteManagers = User::whereIn('user_type', ['Admin', 'Employee'])->select('id', 'username')->get();


        // Return the view and pass data
        return view('admin.tourist_sites.edit', compact('touristSite', 'categories', 'siteManagers'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/tourist-sites/{touristSite}
     *
     * @param  \App\Http\Requests\Admin\UpdateTouristSiteRequest  $request // Use custom request for validation
     * @param  \App\Models\TouristSite  $touristSite // Route Model Binding
     */
    public function update(UpdateTouristSiteRequest $request, TouristSite $touristSite)
    {
        // Validation is handled by UpdateTouristSiteRequest

        DB::beginTransaction();
        $oldImagePath = $touristSite->main_image_url; // Store old path to delete on success

        try {
            $siteData = $request->only([
                'name', 'description', 'location_text', 'latitude',
                'longitude', 'city', 'country', 'category_id', 'video_url',
                'added_by_user_id'
            ]);

             // Handle file upload for main_image if present
            if ($request->hasFile('main_image')) {
                // Upload the new image
                $imagePath = $request->file('main_image')->store('tourist_sites', 'public');
                $siteData['main_image_url'] = '/storage/' . $imagePath; // Save public path

                 // Delete the old image file if it existed
                 if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                      Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
                 }
            } else if ($request->boolean('remove_main_image')) { // Handle checkbox to remove image
                 $siteData['main_image_url'] = null;
                 if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                     Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
                 }
            }

            // Set country default if null (important if form can send null)
             if (isset($siteData['country']) && is_null($siteData['country'])) {
                 $siteData['country'] = 'Syria'; // Or based on your logic
             }


            // Update the TouristSite
            $touristSite->update($siteData);

            DB::commit(); // Commit transaction

            return redirect()->route('admin.tourist-sites.index')->with('success', 'Tourist Site updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            // If a new file was uploaded, clean it up on error
            if (isset($imagePath)) {
                 Storage::disk('public')->delete($imagePath);
            }
            Log::error('Error updating tourist site: ' . $e->getMessage(), ['site_id' => $touristSite->id, 'exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error updating tourist site: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/tourist-sites/{touristSite}
     *
     * @param  \App\Models\TouristSite  $touristSite // Route Model Binding
     */
    public function destroy(TouristSite $touristSite)
    {
         DB::beginTransaction();
         $oldImagePath = $touristSite->main_image_url; // Store old path to delete

         try {
             // Optional: Check for related data (Activities, Experiences)
             // If your migrations have 'restrict' on foreign keys like site_id
             // in tourist_activities or site_experiences, the delete will fail
             // and throw an exception if related records exist.
             // Add checks here if needed for more specific error messages.
             // if ($touristSite->activities()->count() > 0 || $touristSite->experiences()->count() > 0) {
             //      return redirect()->route('admin.tourist-sites.index')->with('error', 'Cannot delete site with associated activities or experiences.');
             // }


             $touristSite->delete(); // This triggers deletion. If using soft deletes, it marks as deleted.

             DB::commit(); // Commit transaction

             // Delete the image file after successful DB deletion
             if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                  Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
             }

             return redirect()->route('admin.tourist-sites.index')->with('success', 'Tourist Site deleted successfully!');

         } catch (\Exception $e) {
             DB::rollBack(); // Rollback transaction
             Log::error('Error deleting tourist site: ' . $e->getMessage(), ['site_id' => $touristSite->id, 'exception' => $e]);
              // Catch specific Integrity Constraint Violation exceptions if needed
              if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                   return redirect()->route('admin.tourist-sites.index')->with('error', 'Site cannot be deleted due to associated data (e.g., activities, experiences).');
              }
             return redirect()->route('admin.tourist-sites.index')->with('error', 'Error deleting tourist site: ' . $e->getMessage());
         }
    }
}