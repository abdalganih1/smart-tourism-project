<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelRoom; // Import HotelRoom model
use App\Models\Hotel; // Import Hotel model (for dropdown)
use App\Models\HotelRoomType; // Import HotelRoomType model (for dropdown)
use App\Http\Requests\Admin\StoreHotelRoomRequest; // Import custom Store Request
use App\Http\Requests\Admin\UpdateHotelRoomRequest; // Import custom Update Request
use Illuminate\Support\Facades\DB; // For database transactions
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // For authorization check (optional, handled by middleware/policy)


class HotelRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/hotel-rooms
     * Optional: Filter by hotel_id
     */
    public function index(Request $request)
    {
        // Fetch hotel rooms with their hotel and type relationships, paginated
        $query = HotelRoom::with(['hotel:id,name', 'type:id,name']); // Load simplified hotel and type

        // Apply filter by hotel_id if provided in the request
        if ($request->filled('hotel_id')) {
             $query->where('hotel_id', $request->hotel_id);
        }

        $hotelRooms = $query->orderBy('hotel_id')->orderBy('room_number') // Order by hotel then room number
                             ->paginate(10); // Paginate results

        // Optional: Pass list of hotels to view for filtering dropdown
        $hotels = Hotel::select('id', 'name')->orderBy('name')->get();
        $currentHotelId = $request->hotel_id; // Pass current filter value

        // Return the view and pass the data
        return view('admin.hotel_rooms.index', compact('hotelRooms', 'hotels', 'currentHotelId'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/hotel-rooms/create
     * Optional: Pre-select hotel if hotel_id is in the request
     */
    public function create(Request $request)
    {
        // Fetch necessary data for the form
        $hotels = Hotel::select('id', 'name')->orderBy('name')->get(); // Get all hotels
        $roomTypes = HotelRoomType::select('id', 'name')->orderBy('name')->get(); // Get all room types

        // Optional: Pre-select hotel if hotel_id is provided
        $preselectedHotelId = $request->hotel_id;

        // Return the view and pass data
        return view('admin.hotel_rooms.create', compact('hotels', 'roomTypes', 'preselectedHotelId'));
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/hotel-rooms
     *
     * @param  \App\Http\Requests\Admin\StoreHotelRoomRequest  $request // Use custom request for validation
     */
    public function store(StoreHotelRoomRequest $request)
    {
        // Validation is handled by StoreHotelRoomRequest

        try {
            $roomData = $request->only([
                'hotel_id', 'room_type_id', 'room_number', 'price_per_night',
                'area_sqm', 'max_occupancy', 'description', 'is_available_for_booking'
            ]);

            // Create the HotelRoom
            HotelRoom::create($roomData);

            // Redirect back to index, potentially filtered by hotel
            return redirect()->route('admin.hotel-rooms.index', ['hotel_id' => $request->hotel_id])->with('success', 'Hotel room created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating hotel room: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error creating hotel room: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/hotel-rooms/{hotelRoom}
     *
     * @param  \App\Models\HotelRoom  $hotelRoom // Route Model Binding
     */
    public function show(HotelRoom $hotelRoom)
    {
        // Load relationships for the detailed view
        $hotelRoom->load(['hotel:id,name', 'type:id,name', 'bookings']); // Load hotel, type, and bookings

        // Return the view and pass the data
        return view('admin.hotel_rooms.show', compact('hotelRoom'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/hotel-rooms/{hotelRoom}/edit
     *
     * @param  \App\Models\HotelRoom  $hotelRoom // Route Model Binding
     */
    public function edit(HotelRoom $hotelRoom)
    {
        // Load relationships for the edit form
        $hotelRoom->load(['hotel:id,name', 'type:id,name']);

        // Fetch necessary data for the form (same as create)
        $hotels = Hotel::select('id', 'name')->orderBy('name')->get();
        $roomTypes = HotelRoomType::select('id', 'name')->orderBy('name')->get();

        // Return the view and pass data
        return view('admin.hotel_rooms.edit', compact('hotelRoom', 'hotels', 'roomTypes'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/hotel-rooms/{hotelRoom}
     *
     * @param  \App\Http\Requests\Admin\UpdateHotelRoomRequest  $request // Use custom request for validation
     * @param  \App\Models\HotelRoom  $hotelRoom // Route Model Binding
     */
    public function update(UpdateHotelRoomRequest $request, HotelRoom $hotelRoom)
    {
         // Validation is handled by UpdateHotelRoomRequest

        try {
            $roomData = $request->only([
                'hotel_id', 'room_type_id', 'room_number', 'price_per_night',
                'area_sqm', 'max_occupancy', 'description', 'is_available_for_booking'
            ]);

            // Update the HotelRoom
            $hotelRoom->update($roomData);

            // Redirect back to index, potentially filtered by hotel
            return redirect()->route('admin.hotel-rooms.index', ['hotel_id' => $request->hotel_id ?? $hotelRoom->hotel_id])->with('success', 'Hotel room updated successfully!');

        } catch (\Exception $e) {
             Log::error('Error updating hotel room: ' . $e->getMessage(), ['room_id' => $hotelRoom->id, 'exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error updating hotel room: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/hotel-rooms/{hotelRoom}
     *
     * @param  \App\Models\HotelRoom  $hotelRoom // Route Model Binding
     */
    public function destroy(HotelRoom $hotelRoom)
    {
         try {
             // Optional: Check for related bookings
             // If your migrations have 'restrict' on foreign key room_id in hotel_bookings,
             // the delete will fail and throw an exception if bookings exist.
             // Add checks here if needed for more specific error messages.
             if ($hotelRoom->bookings()->count() > 0) {
                  return redirect()->route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id])->with('error', 'Cannot delete room as it has associated bookings.');
             }

             $hotelRoom->delete(); // This attempts deletion

             return redirect()->route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id])->with('success', 'Hotel room deleted successfully!');

         } catch (\Exception $e) {
             Log::error('Error deleting hotel room: ' . $e->getMessage(), ['room_id' => $hotelRoom->id, 'exception' => $e]);
             // Catch specific Integrity Constraint Violation exceptions if needed
              if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                   return redirect()->route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id])->with('error', 'Room cannot be deleted due to associated bookings.');
              }
             return redirect()->route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id])->with('error', 'Error deleting hotel room: ' . $e->getMessage());
         }
    }
}