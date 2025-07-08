<?php

namespace App\Http\Controllers\HotelManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel; // Import models

class DashboardController extends Controller
{
    /**
     * Display the hotel manager dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Get hotels managed by this user
        $managedHotels = $user->hotelsManaged;

        // Fetch counts or data relevant to hotels managed by this user
        $hotelCount = $managedHotels->count();
        $totalRooms = $managedHotels->sum(function ($hotel) {
            return $hotel->rooms()->count();
        });
        // Example: Count of upcoming bookings for their hotels
        $upcomingBookingsCount = \App\Models\HotelBooking::whereIn('room_id', $managedHotels->pluck('rooms')->flatten()->pluck('id'))
            ->where('check_in_date', '>=', now()->startOfDay())
            ->where('booking_status', 'Confirmed') // Or other relevant statuses
            ->count();


        return view('hotelmanager.dashboard', compact('managedHotels', 'hotelCount', 'totalRooms', 'upcomingBookingsCount'));
    }
}