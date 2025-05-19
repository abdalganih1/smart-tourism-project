<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelBooking; // Import HotelBooking model
use App\Models\User; // Import User model (for user relationship)
use App\Models\HotelRoom; // Import HotelRoom model (for room relationship)
use App\Models\Hotel; // Import Hotel model (nested relationship via room)
use App\Models\HotelRoomType; // Import HotelRoomType model (nested relationship via room)
// No need for Store/Update/Destroy requests if only index/show are used via resource route
// If adding update status functionality, you'd need a custom request.
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // For authorization check (optional, handled by middleware/policy)


class HotelBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/hotel-bookings
     * Optional: Filter by hotel, room, user, status, dates
     */
    public function index(Request $request)
    {
        // Fetch hotel bookings with necessary relationships, paginated
        $query = HotelBooking::with(['user:id,username', 'room.hotel:id,name', 'room.type:id,name']); // Load user, room, nested hotel and type

        // Optional: Add filtering logic based on request parameters
        // Example: Filter by booking status
        if ($request->filled('status')) {
            $query->where('booking_status', $request->status);
        }
        // Example: Filter by hotel_id
        if ($request->filled('hotel_id')) {
             $query->whereHas('room', function($q) use ($request){
                 $q->where('hotel_id', $request->hotel_id);
             });
         }
         // Example: Filter by user_id
         if ($request->filled('user_id')) {
              $query->where('user_id', $request->user_id);
          }
         // ... add more filters (room_type, dates, etc.)


        $hotelBookings = $query->orderBy('booked_at', 'desc') // Order by booking date
                               ->paginate(15); // Paginate results

        // Optional: Pass data for filter dropdowns
        $hotels = Hotel::select('id', 'name')->orderBy('name')->get();
        $users = User::select('id', 'username')->orderBy('username')->get();
        // Define possible statuses for filter
        $statuses = ['PendingConfirmation', 'Confirmed', 'CancelledByUser', 'CancelledByHotel', 'Completed', 'NoShow'];

        // Return the view and pass the data
        return view('admin.hotel_bookings.index', compact('hotelBookings', 'hotels', 'users', 'statuses'));
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/hotel-bookings/{hotelBooking}
     *
     * @param  \App\Models\HotelBooking  $hotelBooking // Route Model Binding
     */
    public function show(HotelBooking $hotelBooking)
    {
        // Load relationships for the detailed view
        // Load user with profile, and room with nested hotel and type
        $hotelBooking->load(['user.profile', 'room.hotel', 'room.type']);

        // Return the view and pass the data
        return view('admin.hotel_bookings.show', compact('hotelBooking'));
    }

    // create, store, edit, update, destroy methods are not used
    // based on the Route::resource(...)->only(['index', 'show']) definition in web.php
    // If you need to add functionality like "Update Status", you'd add a custom method here
    // and define a custom route/request for it (e.g., POST /admin/hotel-bookings/{hotelBooking}/update-status)
}