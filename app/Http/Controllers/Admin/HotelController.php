<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel; // Import Hotel model
use App\Models\User; // Import User model (for managers)
use App\Http\Requests\Admin\StoreHotelRequest; // Import custom Store Request
use App\Http\Requests\Admin\UpdateHotelRequest; // Import custom Update Request
use Illuminate\Support\Facades\Storage; // For file storage
use Illuminate\Support\Facades\DB; // For database transactions
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // For authorization check (optional, handled by middleware/policy)


class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/hotels
     */
    public function index()
    {
        // Fetch hotels with their managedBy relationship, paginated
        $hotels = Hotel::with(['managedBy:id,username']) // Load manager's basic info
                       ->orderBy('created_at', 'desc')
                       ->paginate(10); // Paginate results

        // Return the view and pass the data
        return view('admin.hotels.index', compact('hotels'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/hotels/create
     */
    public function create()
    {
        // Fetch users who can manage hotels (HotelBookingManager, Admin). Pass only ID and username.
        $managers = User::whereIn('user_type', ['HotelBookingManager', 'Admin'])->select('id', 'username')->get();

        // Return the view and pass data
        return view('admin.hotels.create', compact('managers'));
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/hotels
     *
     * @param  \App\Http\Requests\Admin\StoreHotelRequest  $request // Use custom request for validation
     */
    public function store(StoreHotelRequest $request)
    {
        // Validation is handled by StoreHotelRequest

        DB::beginTransaction();

        try {
            $hotelData = $request->only([
                'name', 'star_rating', 'description', 'address_line1',
                'city', 'country', 'latitude', 'longitude', 'contact_phone',
                'contact_email', 'managed_by_user_id'
            ]);

            // Handle file upload for main_image if present
            if ($request->hasFile('main_image')) {
                $imagePath = $request->file('main_image')->store('hotels', 'public'); // Store in 'storage/app/public/hotels'
                $hotelData['main_image_url'] = '/storage/' . $imagePath; // Save public path
            }

            // Set country default if not provided and column is nullable (though schema has default 'Syria')
             if (!isset($hotelData['country'])) {
                 $hotelData['country'] = 'Syria';
             }

            // Create the Hotel
            $hotel = Hotel::create($hotelData);

            DB::commit(); // Commit transaction

            return redirect()->route('admin.hotels.index')->with('success', 'Hotel created successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            // Clean up uploaded file if transaction failed and file was stored
            if (isset($imagePath)) {
                 Storage::disk('public')->delete($imagePath);
            }
            Log::error('Error creating hotel: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error creating hotel: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/hotels/{hotel}
     *
     * @param  \App\Models\Hotel  $hotel // Route Model Binding
     */
    public function show(Hotel $hotel)
    {
        // Load relationships for the detailed view
        $hotel->load(['managedBy.profile', 'rooms.type', 'rooms.bookings']); // Load manager with profile, and rooms with type/bookings

        // Return the view and pass the data
        return view('admin.hotels.show', compact('hotel'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/hotels/{hotel}/edit
     *
     * @param  \App\Models\Hotel  $hotel // Route Model Binding
     */
    public function edit(Hotel $hotel)
    {
        // Load relationships for the edit form
        $hotel->load(['managedBy.profile', 'rooms.type']); // Rooms might be managed separately, but helpful to see

        // Fetch necessary data for the form (same as create)
        $managers = User::whereIn('user_type', ['HotelBookingManager', 'Admin'])->select('id', 'username')->get();

        // Return the view and pass data
        return view('admin.hotels.edit', compact('hotel', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/hotels/{hotel}
     *
     * @param  \App\Http\Requests\Admin\UpdateHotelRequest  $request // Use custom request for validation
     * @param  \App\Models\Hotel  $hotel // Route Model Binding
     */
    public function update(UpdateHotelRequest $request, Hotel $hotel)
    {
        // Validation is handled by UpdateHotelRequest

        DB::beginTransaction();
        $oldImagePath = $hotel->main_image_url; // Store old path to delete on success

        try {
            $hotelData = $request->only([
                'name', 'star_rating', 'description', 'address_line1',
                'city', 'country', 'latitude', 'longitude', 'contact_phone',
                'contact_email', 'managed_by_user_id'
            ]);

             // Handle file upload for main_image if present
            if ($request->hasFile('main_image')) {
                // Upload the new image
                $imagePath = $request->file('main_image')->store('hotels', 'public');
                $hotelData['main_image_url'] = '/storage/' . $imagePath; // Save public path

                 // Delete the old image file if it existed
                 if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                      Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
                 }
            } else if ($request->boolean('remove_main_image')) { // Handle checkbox to remove image
                 $hotelData['main_image_url'] = null;
                 if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                     Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
                 }
            }

            // Set country default if null (important if form can send null)
             if (isset($hotelData['country']) && is_null($hotelData['country'])) {
                 $hotelData['country'] = 'Syria'; // Or based on your logic
             }


            // Update the Hotel
            $hotel->update($hotelData);

            DB::commit(); // Commit transaction

            return redirect()->route('admin.hotels.index')->with('success', 'Hotel updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            // If a new file was uploaded, clean it up on error
            if (isset($imagePath)) {
                 Storage::disk('public')->delete($imagePath);
            }
            Log::error('Error updating hotel: ' . $e->getMessage(), ['hotel_id' => $hotel->id, 'exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error updating hotel: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/hotels/{hotel}
     *
     * @param  \App\Models\Hotel  $hotel // Route Model Binding
     */
    public function destroy(Hotel $hotel)
    {
         DB::beginTransaction();
         $oldImagePath = $hotel->main_image_url; // Store old path to delete

         try {
             // Optional: Check for related data (Rooms, Bookings)
             // If your migrations have 'restrict' on foreign keys like hotel_id
             // in hotel_rooms or hotel_bookings, the delete will fail
             // and throw an exception if related records exist.
             // Add checks here if needed for more specific error messages.
             // Note: Deleting a hotel should likely cascade to rooms, which might cascade to bookings.
             // Verify your migration onDelete rules. If Rooms cascade from Hotel, and Bookings restrict from Room,
             // you might need to check Bookings first.
             if ($hotel->rooms()->whereHas('bookings')->count() > 0) {
                  return redirect()->route('admin.hotels.index')->with('error', 'Cannot delete hotel as it has rooms with associated bookings.');
             }
              // If rooms should prevent deletion even without bookings:
             // if ($hotel->rooms()->count() > 0) {
             //      return redirect()->route('admin.hotels.index')->with('error', 'Cannot delete hotel as it has associated rooms.');
             // }


             $hotel->delete(); // This attempts deletion. If using soft deletes, it marks as deleted.

             DB::commit(); // Commit transaction

             // Delete the image file after successful DB deletion
             if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                  Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
             }


             return redirect()->route('admin.hotels.index')->with('success', 'Hotel deleted successfully!');

         } catch (\Exception $e) {
             DB::rollBack(); // Rollback transaction
             Log::error('Error deleting hotel: ' . $e->getMessage(), ['hotel_id' => $hotel->id, 'exception' => $e]);
              // Catch specific Integrity Constraint Violation exceptions if needed
              if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                   return redirect()->route('admin.hotels.index')->with('error', 'Hotel cannot be deleted due to associated data (e.g., rooms, bookings).');
              }
             return redirect()->route('admin.hotels.index')->with('error', 'Error deleting hotel: ' . $e->getMessage());
         }
    }
}