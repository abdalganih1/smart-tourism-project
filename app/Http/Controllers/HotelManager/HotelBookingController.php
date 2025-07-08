<?php

namespace App\Http\Controllers\HotelManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HotelBooking;
use App\Models\HotelRoom;
use App\Models\Hotel;
use App\Models\User;
use App\Http\Requests\HotelBooking\StoreHotelBookingRequest;
use App\Http\Requests\HotelBooking\UpdateHotelBookingRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Gate; // Make sure Gate facade is imported if you use Gate::define here (but define in AuthServiceProvider is better)

class HotelBookingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of bookings for rooms within hotels managed by the authenticated user.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Get IDs of hotels managed by this user
        $managedHotelIds = $user->hotelsManaged->pluck('id');

        // Get IDs of rooms belonging to these hotels
        $managedRoomIds = HotelRoom::whereIn('hotel_id', $managedHotelIds)->pluck('id');

        // Fetch bookings for these rooms, with pagination and eager load necessary relations
        // ** تصحيح سلسلة التحميل المسبق هنا **
        $bookings = HotelBooking::whereIn('room_id', $managedRoomIds)
                               // Load user, room, room's hotel, and room's type
                               ->with('user', 'room.hotel', 'room.type')
                               ->latest('check_in_date')
                               ->paginate(10);

        return view('hotelmanager.hotel_bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create(): View
    {
        // No specific authorization check beyond route middleware is usually needed for showing the creation form.

        // Get managed hotels and their rooms/types for dropdowns
        $managedHotels = Auth::user()->hotelsManaged()->with('rooms.type')->get();

        // You might need a way to search/select users if managers can book for others
        // Fetching all users might be too much data, consider a searchable select or only fetching 'Tourist' types.
        $users = User::where('user_type', 'Tourist')->get(); // Example: Only show Tourist users

        return view('hotelmanager.hotel_bookings.create', compact('managedHotels', 'users'));
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(StoreHotelBookingRequest $request): RedirectResponse
    {
        // Validation handled by StoreHotelBookingRequest

        // Verify the room belongs to a hotel managed by the user BEFORE creating the booking.
        // ** Eager load hotel relationship before authorizing the room **
        $room = HotelRoom::with('hotel')->findOrFail($request->room_id);
        // تأكد من أن Gate 'manage-hotel-room' معرف بشكل صحيح في AuthServiceProvider ويتحقق من ملكية الفندق
        // $this->authorize('manage-hotel-room', $room); // Use the room gate check

        // Optional: Recalculate total amount based on dates and room price
        // $checkIn = \Carbon\Carbon::parse($request->check_in_date);
        // $checkOut = \Carbon\Carbon::parse($request->check_out_date);
        // $numberOfNights = $checkIn->diffInDays($checkOut);
        // $totalAmount = $room->price_per_night * $numberOfNights;
        // $validatedData = $request->validated();
        // $validatedData['total_amount'] = $totalAmount;

        // Create the booking
        // Ensure fields are fillable/guarded appropriately in HotelBooking model
        HotelBooking::create($request->validated()); // If using validatedData with total_amount, pass that instead

        return redirect()->route('hotelmanager.hotel-bookings.index')
                         ->with('success', 'Booking created successfully.');
    }


    /**
     * Display the specified booking.
     */
    public function show(HotelBooking $hotelBooking): View
    {
        // ** Eager load the 'room.hotel' relationship BEFORE authorizing **
        // This is necessary because the 'manage-hotel-booking' Gate checks $hotelBooking->room->hotel->managed_by_user_id
        $hotelBooking->load('room.hotel');

        // Use the Gate to authorize viewing this specific booking
        // تأكد من أن Gate 'manage-hotel-booking' معرف بشكل صحيح في AuthServiceProvider
        // $this->authorize('manage-hotel-booking', $hotelBooking);

        // Eager load other relationships needed for the view (e.g., user, room type)
        $hotelBooking->load('user', 'room.type'); // Ensure user and room type are loaded for display

        return view('hotelmanager.hotel_bookings.show', compact('hotelBooking'));
    }

    /**
     * Show the form for editing the specified booking.
     * Useful for updating status or dates.
     */
    public function edit(HotelBooking $hotelBooking): View
    {
         // ** Eager load the 'room.hotel' relationship BEFORE authorizing **
        $hotelBooking->load('room.hotel');

         // Use the Gate to authorize editing this specific booking
        // تأكد من أن Gate 'manage-hotel-booking' معرف بشكل صحيح في AuthServiceProvider
        // $this->authorize('manage-hotel-booking', $hotelBooking);

        // Get managed hotels/rooms/users for the edit form if dates/rooms can change
         $managedHotels = Auth::user()->hotelsManaged()->with('rooms.type')->get();
         $users = User::where('user_type', 'Tourist')->get(); // Example: Only show Tourist users


        return view('hotelmanager.hotel_bookings.edit', compact('hotelBooking', 'managedHotels', 'users'));
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(UpdateHotelBookingRequest $request, HotelBooking $hotelBooking): RedirectResponse
    {
         // ** Eager load the 'room.hotel' relationship BEFORE authorizing **
        $hotelBooking->load('room.hotel');

        // Use the Gate to authorize updating this specific booking
        // تأكد من أن Gate 'manage-hotel-booking' معرف بشكل صحيح في AuthServiceProvider
        // $this->authorize('manage-hotel-booking', $hotelBooking);

        // Validation handled by UpdateHotelBookingRequest ($request->validated())

        // Update the booking attributes
        $hotelBooking->update($request->validated());

        return redirect()->route('hotelmanager.hotel-bookings.index')
                         ->with('success', 'Booking updated successfully.');
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(HotelBooking $hotelBooking): RedirectResponse
    {
        // ** Eager load the 'room.hotel' relationship BEFORE authorizing **
        $hotelBooking->load('room.hotel');

        // Use the Gate to authorize deleting this specific booking
        // تأكد من أن Gate 'manage-hotel-booking' معرف بشكل صحيح في AuthServiceProvider
        // $this->authorize('manage-hotel-booking', $hotelBooking);

        $hotelBooking->delete();

        return redirect()->route('hotelmanager.hotel-bookings.index')
                         ->with('success', 'Booking deleted successfully.');
    }
}