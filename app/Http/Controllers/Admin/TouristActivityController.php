<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Optional, if needed for logic
use Illuminate\Support\Facades\Storage; // If handling images/videos
use Illuminate\Support\Str; // If using Str helper
use App\Models\TouristActivity; // Import the model
use App\Models\TouristSite; // To link activities to sites
use App\Models\User; // To link activities to organizers (Vendors/Employees?)
use App\Http\Requests\TouristActivity\StoreTouristActivityRequest; // Import Requests
use App\Http\Requests\TouristActivity\UpdateTouristActivityRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Import Trait


class TouristActivityController extends Controller
{
    use AuthorizesRequests; // Use Trait

    /**
     * Display a listing of tourist activities.
     */
    public function index(): View
    {
        // Authorize viewing any activities (e.g., Admins, Employees)
        // Policy should check if user has general permission to view activity list.
        // $this->authorize('viewAny', TouristActivity::class); // Assuming a policy or gate 'viewAny' for TouristActivity

        // Fetch all activities with pagination and eager load necessary relations
        $activities = TouristActivity::with('site', 'organizer')->paginate(10); // Load site and organizer relations

        return view('admin.tourist_activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new activity.
     */
    public function create(): View
    {
        // Authorize creating an activity
        // $this->authorize('create', TouristActivity::class); // Assuming a policy or gate 'create' for TouristActivity

        // Get data for dropdowns/selects in the form
        $sites = TouristSite::all(); // Get all tourist sites to link activity to
        // You might need to get users who can be organizers (e.g., Admins, Employees, Vendors)
        $organizers = User::whereIn('user_type', ['Admin', 'Employee', 'Vendor'])->get(); // Example: Filter by user types

        return view('admin.tourist_activities.create', compact('sites', 'organizers'));
    }

    /**
     * Store a newly created activity in storage.
     */
    public function store(StoreTouristActivityRequest $request): RedirectResponse
    {
        // Authorization check is done by the Request's authorize() method,
        // but you can also do it here using $this->authorize('create', TouristActivity::class);

        // Create the activity using validated data
        // Ensure fields are fillable in TouristActivity model
        $activity = TouristActivity::create($request->validated());

        // Handle image/video uploads if needed (as per your schema's image_url/video_url - though not explicitly in activity schema)
        // If activities have images/videos, add fields and handle uploads here similar to ProductController

        return redirect()->route('admin.tourist-activities.index')
                         ->with('success', 'Tourist activity created successfully.');
    }

    /**
     * Display the specified activity.
     */
    public function show(TouristActivity $touristActivity): View
    {
        // Authorize viewing this specific activity
        // Policy/Gate 'view' on TouristActivity model should check if the user (Admin/Employee) can view it.
        // $this->authorize('view', $touristActivity); // Assuming a policy or gate 'view' for TouristActivity

        // Eager load relationships needed for the show view
        $touristActivity->load('site', 'organizer'); // Load site and organizer relations

        return view('admin.tourist_activities.show', compact('touristActivity'));
    }

    /**
     * Show the form for editing the specified activity.
     */
    public function edit(TouristActivity $touristActivity): View
    {
        // Authorize editing this specific activity
        // $this->authorize('update', $touristActivity); // Assuming a policy or gate 'update' for TouristActivity

        // Get data for dropdowns/selects in the form
        $sites = TouristSite::all();
        $organizers = User::whereIn('user_type', ['Admin', 'Employee', 'Vendor'])->get(); // Example: Filter by user types

        return view('admin.tourist_activities.edit', compact('touristActivity', 'sites', 'organizers'));
    }

    /**
     * Update the specified activity in storage.
     */
    public function update(UpdateTouristActivityRequest $request, TouristActivity $touristActivity): RedirectResponse
    {
        // Authorization check done by Request's authorize() and/or here
        // $this->authorize('update', $touristActivity);

        // Update the activity using validated data
        $touristActivity->update($request->validated());

        // Handle image/video updates if applicable

        return redirect()->route('admin.tourist-activities.index')
                         ->with('success', 'Tourist activity updated successfully.');
    }

    /**
     * Remove the specified activity from storage.
     */
    public function destroy(TouristActivity $touristActivity): RedirectResponse
    {
        // Authorize deleting this specific activity
        // $this->authorize('delete', $touristActivity); // Assuming a policy or gate 'delete' for TouristActivity

        // Delete the activity
        $touristActivity->delete();

        // Optional: Handle deletion of associated files (images/videos)

        return redirect()->route('admin.tourist-activities.index')
                         ->with('success', 'Tourist activity deleted successfully.');
    }
}