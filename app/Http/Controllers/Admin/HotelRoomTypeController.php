<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelRoomType; // Import HotelRoomType model
use App\Http\Requests\Admin\StoreHotelRoomTypeRequest; // Import custom Store Request
use App\Http\Requests\Admin\UpdateHotelRoomTypeRequest; // Import custom Update Request
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // For authorization check (optional, handled by middleware/policy)


class HotelRoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/hotel-room-types
     */
    public function index()
    {
        // Fetch all hotel room types, ordered by name
        // No pagination needed for a lookup table unless it's huge
        $roomTypes = HotelRoomType::orderBy('name')->get();

        // Return the view and pass the data
        return view('admin.hotel_room_types.index', compact('roomTypes'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/hotel-room-types/create
     */
    public function create()
    {
        // Return the view for creating a room type
        return view('admin.hotel_room_types.create');
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/hotel-room-types
     *
     * @param  \App\Http\Requests\Admin\StoreHotelRoomTypeRequest  $request // Use custom request for validation
     */
    public function store(StoreHotelRoomTypeRequest $request)
    {
        // Validation is handled by StoreHotelRoomTypeRequest

        try {
            $roomTypeData = $request->only(['name', 'description']);

            // Create the HotelRoomType
            HotelRoomType::create($roomTypeData);

            return redirect()->route('admin.hotel-room-types.index')->with('success', 'Hotel room type created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating hotel room type: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error creating hotel room type: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/hotel-room-types/{hotelRoomType}
     * Note: Show is less common for simple lookup tables. Can be omitted.
     *
     * @param  \App\Models\HotelRoomType  $hotelRoomType // Route Model Binding
     */
    public function show(HotelRoomType $hotelRoomType)
    {
         // Redirect to edit page as show page is often redundant for simple resources
         return redirect()->route('admin.hotel-room-types.edit', $hotelRoomType);

        // // Or return a show view if you create one
        // return view('admin.hotel_room_types.show', compact('hotelRoomType'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/hotel-room-types/{hotelRoomType}/edit
     *
     * @param  \App\Models\HotelRoomType  $hotelRoomType // Route Model Binding
     */
    public function edit(HotelRoomType $hotelRoomType)
    {
        // Return the view for editing a room type and pass the data
        return view('admin.hotel_room_types.edit', compact('hotelRoomType'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/hotel-room-types/{hotelRoomType}
     *
     * @param  \App\Http\Requests\Admin\UpdateHotelRoomTypeRequest  $request // Use custom request for validation
     * @param  \App\Models\HotelRoomType  $hotelRoomType // Route Model Binding
     */
    public function update(UpdateHotelRoomTypeRequest $request, HotelRoomType $hotelRoomType)
    {
         // Validation is handled by UpdateHotelRoomTypeRequest

        try {
            $roomTypeData = $request->only(['name', 'description']);

            // Update the HotelRoomType
            $hotelRoomType->update($roomTypeData);

            return redirect()->route('admin.hotel-room-types.index')->with('success', 'Hotel room type updated successfully!');

        } catch (\Exception $e) {
             Log::error('Error updating hotel room type: ' . $e->getMessage(), ['room_type_id' => $hotelRoomType->id, 'exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error updating hotel room type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/hotel-room-types/{hotelRoomType}
     *
     * @param  \App\Models\HotelRoomType  $hotelRoomType // Route Model Binding
     */
    public function destroy(HotelRoomType $hotelRoomType)
    {
         try {
             // Check if any rooms use this room type (based on restrict delete in migration)
             // If rooms exist, deleting the type will fail due to foreign key constraint.
             // We can add a check here for a more user-friendly error message before deletion attempt.
             if ($hotelRoomType->rooms()->count() > 0) {
                  return redirect()->route('admin.hotel-room-types.index')->with('error', 'Cannot delete room type as it is used by existing rooms.');
             }


             $hotelRoomType->delete(); // This attempts deletion

             return redirect()->route('admin.hotel-room-types.index')->with('success', 'Hotel room type deleted successfully!');

         } catch (\Exception $e) {
             Log::error('Error deleting hotel room type: ' . $e->getMessage(), ['room_type_id' => $hotelRoomType->id, 'exception' => $e]);
             // Catch specific Integrity Constraint Violation exceptions if needed
              if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                   return redirect()->route('admin.hotel-room-types.index')->with('error', 'Room type cannot be deleted due to associated rooms.');
              }
             return redirect()->route('admin.hotel-room-types.index')->with('error', 'Error deleting hotel room type: ' . $e->getMessage());
         }
    }
}