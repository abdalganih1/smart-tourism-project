<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel; // Import Hotel model
use App\Http\Resources\HotelResource; // Import Hotel Resource
// We won't need API Store/Update requests for *this* public browse controller
// use App\Http\Requests\Api\StoreHotelRequest;
// use App\Http\Requests\Api\UpdateHotelRequest;
use Illuminate\Support\Facades\Log; // For logging


class HotelController extends Controller
{
    /**
     * Display a listing of hotels.
     * Accessible at GET /api/hotels
     * Can be public.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Start building the query
        $query = Hotel::query();

        // Optional: Add filtering logic based on request parameters
        // Example: Filter by city
        if ($request->filled('city')) {
            $query->where('city', 'LIKE', "%{$request->city}%");
        }
        // Example: Filter by star rating (min rating)
         if ($request->filled('min_stars')) {
              $query->where('star_rating', '>=', $request->min_stars);
          }
         // Example: Filter by manager_id
         if ($request->filled('managed_by_user_id')) {
              $query->where('managed_by_user_id', $request->managed_by_user_id);
          }
        // Example: Search by name or description
         if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
         }

        // Example: Order by name or rating
        $query->orderBy('name');

        // Paginate the results
        $hotels = $query->paginate(15); // Adjust pagination size as needed

        // Return the collection of hotels using HotelResource
        return HotelResource::collection($hotels);
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /api/hotels
     * Note: This endpoint is typically for authenticated Admin/HotelBookingManager, not public users.
     */
    public function store(Request $request) // Using base Request as it's not a public endpoint
    {
         // Prevent creation via this public endpoint
         return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Display the specified hotel.
     * Accessible at GET /api/hotels/{hotel}
     * Can be public.
     *
     * @param  \App\Models\Hotel  $hotel // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function show(Hotel $hotel)
    {
        // Load relationships needed for the show view (e.g., managedBy, rooms with type)
        $hotel->load(['managedBy.profile', 'rooms.type']);

        // Optional: Load polymorphic relationships if you want to show comments/ratings here
        // $hotel->load(['managedBy.profile', 'rooms.type', 'comments.user.profile', 'ratings.user.profile']);

        // Return the single hotel using HotelResource
        return new HotelResource($hotel);
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /api/hotels/{hotel}
     * Note: This endpoint is typically for authenticated Admin/HotelBookingManager.
     */
    public function update(Request $request, Hotel $hotel) // Using base Request as it's not a public endpoint
    {
        // Prevent updates via this public endpoint
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /api/hotels/{hotel}
     * Note: This endpoint is typically for authenticated Admin.
     */
    public function destroy(Hotel $hotel) // Using base Request as it's not a public endpoint
    {
        // Prevent deletion via this public endpoint
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

     // --- Optional Methods for Polymorphic Relationships (if fetching via hotel endpoint) ---
     // (Similar methods to CommentController for fetching comments, ratings, isFavorited for this hotel)
     // You would add methods like 'comments', 'ratings', 'isFavoritedForTarget' here or use a shared trait.

     /**
      * Get rooms for a specific hotel.
      * Accessible at GET /api/hotels/{hotel}/rooms (Requires a route definition)
      * Can be public.
      *
      * @param  \App\Models\Hotel  $hotel // Route Model Binding for the hotel
      * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
      */
      public function rooms(Hotel $hotel)
      {
          // Fetch rooms for this specific hotel
          // Load the type relationship for each room
           $rooms = $hotel->rooms()->with('type:id,name')->orderBy('room_number')->get(); // No pagination needed if list is short

           // You'll need a HotelRoomResource to format rooms
           return \App\Http\Resources\HotelRoomResource::collection($rooms); // Using full namespace to avoid import if not in same file
      }
}