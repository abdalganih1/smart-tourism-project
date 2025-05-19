<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TouristActivity; // Import TouristActivity model
use App\Models\TouristSite; // Import TouristSite model (for dropdown/filter)
use App\Http\Resources\TouristActivityResource; // Import TouristActivity Resource


class TouristActivityController extends Controller
{
    /**
     * Display a listing of tourist activities.
     * Accessible at GET /api/tourist-activities
     * Can be public.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Start building the query
        $query = TouristActivity::query();

        // Optional: Add filtering logic based on request parameters
        // Example: Filter by site_id
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }
        // Example: Filter by organizer_user_id
         if ($request->filled('organizer_user_id')) {
              $query->where('organizer_user_id', $request->organizer_user_id);
          }
        // Example: Filter by date range (start_datetime)
        if ($request->filled('date_from')) {
            $query->where('start_datetime', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
             // Add a day to the 'to' date to include activities on that day
             $dateTo = \Carbon\Carbon::parse($request->date_to)->addDay();
             $query->where('start_datetime', '<', $dateTo);
         }
        // Example: Search by name or description
         if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
         }

        // Example: Order results by start date
        $query->orderBy('start_datetime');

        // Paginate the results
        $activities = $query->paginate(15); // Adjust pagination size as needed

        // Return the collection using TouristActivityResource
        return TouristActivityResource::collection($activities);
    }

    /**
     * Store a newly created resource in storage.
     * Note: Not for public API. Activities are created via Admin/Employee panel.
     */
    public function store(Request $request)
    {
         return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Display the specified tourist activity.
     * Accessible at GET /api/tourist-activities/{touristActivity}
     * Can be public.
     *
     * @param  \App\Models\TouristActivity  $touristActivity // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function show(TouristActivity $touristActivity)
    {
        // Load relationships needed for the show view (e.g., site, organizer)
        $touristActivity->load(['site:id,name,city', 'organizer:id,username']);

        // Return the single activity using TouristActivityResource
        return new TouristActivityResource($touristActivity);
    }

    /**
     * Update the specified resource in storage.
     * Note: Not for public API.
     */
    public function update(Request $request, TouristActivity $touristActivity)
    {
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Remove the specified resource from storage.
     * Note: Not for public API.
     */
    public function destroy(TouristActivity $touristActivity)
    {
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    // No polymorphic relations directly on TouristActivity in schema V2.1
    // (Comments/Ratings/Favorites would target SiteExperience or TouristSite, not Activity)
}