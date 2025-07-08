<?php

namespace App\Http\Controllers\HotelManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use App\Http\Requests\Hotel\UpdateHotelRequest; // Assuming you created this request
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // ** استيراد Trait AuthorizesRequests **
use Gate;
use app\Models\User;
class HotelController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the hotels managed by the authenticated user.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Fetch only hotels managed by this user, with pagination
        $hotels = $user->hotelsManaged()->paginate(10); // Assuming 'hotelsManaged' relationship on User

        return view('hotelmanager.hotels.index', compact('hotels'));
    }

    /**
     * Display the specified managed hotel.
     */
    public function show(Hotel $hotel): View
    {
        // Use the Gate to authorize viewing this specific hotel
Gate::define('manage-hotel', function (User $user, Hotel $hotel) {
    return $user->isHotelBookingManager() && $user->id === $hotel->managed_by_user_id;
});
        // Eager load relationships needed for the show view (e.g., rooms, maybe bookings summary)
        $hotel->load('rooms.type'); // Load rooms and their types

        return view('hotelmanager.hotels.show', compact('hotel'));
    }

    /**
     * Show the form for editing the specified managed hotel.
     */
    public function edit(Hotel $hotel): View
    {
         // Use the Gate to authorize editing this specific hotel
Gate::define('manage-hotel', function (User $user, Hotel $hotel) {
    return $user->isHotelBookingManager() && $user->id === $hotel->managed_by_user_id;
});
        return view('hotelmanager.hotels.edit', compact('hotel'));
    }

    /**
     * Update the specified managed hotel in storage.
     */
    public function update(UpdateHotelRequest $request, Hotel $hotel): RedirectResponse
    {
        // Use the Gate to authorize updating this specific hotel
        Gate::define('manage-hotel', function (User $user, Hotel $hotel) {
            return $user->isHotelBookingManager() && $user->id === $hotel->managed_by_user_id;
        });

        // Validation is handled by UpdateHotelRequest

        // Update the hotel attributes (excluding managed_by_user_id or other fields not manageable here)
        // Ensure fields like 'name', 'star_rating', 'description', 'address_line1', etc. are fillable on Hotel model
        $hotel->update($request->validated());

        // Handle image upload/deletion if needed

        return redirect()->route('hotelmanager.hotels.index') // Or ->route('hotelmanager.hotels.show', $hotel)
                         ->with('success', 'Hotel updated successfully.');
    }

    /**
     * Show the form for creating a new resource. (Not used by 'only' routes)
     */
    public function create() { abort(404); }
    /**
     * Store a newly created resource in storage. (Not used by 'only' routes)
     */
    public function store(Request $request) { abort(404); }
    /**
     * Remove the specified resource from storage. (Not used by 'only' routes)
     */
    public function destroy(string $id) { abort(404); }
}