<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelBooking; // Import HotelBooking model
use App\Models\HotelRoom; // Import HotelRoom model (needed for booking logic)
use App\Http\Requests\Api\StoreHotelBookingRequest; // Import custom Store Request
// No need for Update request as booking details (dates, room, guests, amount) are usually not editable after booking.
// use App\Http\Requests\Api\UpdateHotelBookingRequest;
use App\Http\Resources\HotelBookingResource; // Import HotelBooking Resource
use Illuminate\Support\Facades\Auth; // For authentication and user checks
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Auth\Access\AuthorizationException; // For authorization errors
use Illuminate\Support\Facades\DB; // For database transactions
use Carbon\Carbon; // For date calculations


class HotelBookingController extends Controller
{
    /**
     * Display a listing of the authenticated user's hotel bookings.
     * Accessible at GET /api/my-bookings (Custom route name suggested)
     * Requires authentication.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // Authentication check handled by 'auth:sanctum' middleware

        // Fetch the authenticated user's hotel bookings
        // Eager load necessary relationships (room, nested hotel and type)
        $hotelBookings = Auth::user()->hotelBookings()
                                     ->with(['room.hotel:id,name,city', 'room.type:id,name']) // Load room, hotel, type (basic info)
                                     ->orderBy('check_in_date', 'desc') // Order by check-in date
                                     ->paginate(15); // Paginate results

        // Return the collection of bookings using HotelBookingResource
        return HotelBookingResource::collection($hotelBookings);
    }

    /**
     * Store a new hotel booking for the authenticated user.
     * Accessible at POST /api/bookings (Custom route name suggested)
     * Requires authentication.
     *
     * @param  \App\Http\Requests\Api\StoreHotelBookingRequest  $request // Use custom request for validation
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\HotelBookingResource
     */
    public function store(StoreHotelBookingRequest $request)
    {
        // Validation is handled by StoreHotelBookingRequest
        // Authentication check handled by 'auth:sanctum' middleware

        $userId = Auth::id();
        $roomId = $request->room_id;
        $checkInDate = Carbon::parse($request->check_in_date);
        $checkOutDate = Carbon::parse($request->check_out_date);

        // --- Business Logic / Availability Check ---
        // 1. Get the selected room
        $room = HotelRoom::findOrFail($roomId);

        // 2. Check if the room is generally available for booking
        if (!$room->is_available_for_booking) {
             return response()->json(['message' => 'This room is currently not available for booking.'], 400);
        }

        // 3. Check for booking date overlaps
        $overlappingBookings = HotelBooking::where('room_id', $roomId)
                                           ->where(function ($query) use ($checkInDate, $checkOutDate) {
                                               $query->whereBetween('check_in_date', [$checkInDate, $checkOutDate->copy()->subDay()]) // Booking starts within requested dates (excluding last day)
                                                     ->orWhereBetween('check_out_date', [$checkInDate->copy()->addDay(), $checkOutDate]) // Booking ends within requested dates (excluding first day)
                                                     ->orWhere(function ($q) use ($checkInDate, $checkOutDate) { // Existing booking spans the requested dates
                                                         $q->where('check_in_date', '<=', $checkInDate)
                                                           ->where('check_out_date', '>=', $checkOutDate);
                                                     });
                                           })
                                           ->whereIn('booking_status', ['PendingConfirmation', 'Confirmed']) // Only check against active/pending bookings
                                           ->exists();

        if ($overlappingBookings) {
            return response()->json(['message' => 'This room is not available for the selected dates.'], 400);
        }

        // 4. Calculate total amount
        $numberOfNights = $checkInDate->diffInDays($checkOutDate);
        if ($numberOfNights <= 0) {
             return response()->json(['message' => 'Check-out date must be after check-in date.'], 400);
        }
        $totalAmount = $numberOfNights * $room->price_per_night;


        // --- Create Booking ---
        DB::beginTransaction();

        try {
             $bookingData = $request->only(['num_adults', 'num_children', 'special_requests']);
             $bookingData['user_id'] = $userId;
             $bookingData['room_id'] = $roomId;
             $bookingData['check_in_date'] = $checkInDate;
             $bookingData['check_out_date'] = $checkOutDate;
             $bookingData['total_amount'] = $totalAmount;
             // booking_status defaults to 'PendingConfirmation' in migration
             // payment_status defaults to 'Unpaid' in migration
             // booked_at defaults to current timestamp in migration

            $hotelBooking = HotelBooking::create($bookingData);

            // Note: Payment processing integration would go here or in a separate step after booking is created.
            // The payment_transaction_id would be saved after successful payment.

            DB::commit(); // Commit transaction

            // Load relationships for the resource response
            $hotelBooking->load(['user.profile', 'room.hotel', 'room.type']);

            // Return the created booking using HotelBookingResource
            return new HotelBookingResource($hotelBooking);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            Log::error('Error creating hotel booking: ' . $e->getMessage(), ['user_id' => $userId, 'request_data' => $request->all(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to create booking. Please try again.'], 500);
        }
    }

    /**
     * Display a specific hotel booking for the authenticated user.
     * Accessible at GET /api/my-bookings/{hotelBooking}
     * Requires authentication and authorization (user owns the booking).
     *
     * @param  \App\Models\HotelBooking  $hotelBooking // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function show(HotelBooking $hotelBooking)
    {
         // Authentication check handled by middleware

         // Authorization: Ensure the authenticated user owns this booking
         if ($hotelBooking->user_id !== Auth::id()) {
              throw new AuthorizationException('You do not own this booking.');
              // Or return a JSON error response:
              // return response()->json(['message' => 'You are not authorized to view this booking.'], 403); // 403 Forbidden
         }

         // Load relationships needed for the resource response
        $hotelBooking->load(['user.profile', 'room.hotel', 'room.type']);

        // Return the single booking using HotelBookingResource
        return new HotelBookingResource($hotelBooking);
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /api/my-bookings/{hotelBooking}
     * Note: Updating a booking (dates, room, guests) is complex and often not allowed via API.
     * Status updates (e.g., Cancel) might be a separate action.
     *
     * @param  \Illuminate\Http\Request  $request // Using base Request as it's not a primary update endpoint
     * @param  \App\Models\HotelBooking  $hotelBooking // Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, HotelBooking $hotelBooking)
    {
         // Prevent standard updates via this endpoint
         // You might implement status updates here based on specific roles (e.g., Admin/HotelManager)
         // or create a separate 'cancel' method.
         return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Remove the specified resource from storage (e.g., Cancel a booking).
     * Accessible at DELETE /api/my-bookings/{hotelBooking}
     * Requires authentication and authorization (user owns the booking).
     *
     * @param  \App\Models\HotelBooking  $hotelBooking // Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(HotelBooking $hotelBooking)
    {
         // Authentication check handled by middleware

         // Authorization: Ensure the authenticated user owns this booking
         if ($hotelBooking->user_id !== Auth::id()) {
              throw new AuthorizationException('You do not own this booking.');
              // Or return a JSON error response:
              // return response()->json(['message' => 'You are not authorized to cancel this booking.'], 403); // 403 Forbidden
         }

         // --- Business Logic / Cancellation Rules ---
         // 1. Check if the booking can be cancelled (e.g., not completed, not too close to check-in date, not already cancelled)
         $allowCancellationStatuses = ['PendingConfirmation', 'Confirmed']; // Allow cancelling only these statuses
         $checkInDate = Carbon::parse($hotelBooking->check_in_date);
         $daysUntilCheckIn = now()->diffInDays($checkInDate, false); // Count days until check-in, false for absolute difference


         if (!in_array($hotelBooking->booking_status, $allowCancellationStatuses)) {
             return response()->json(['message' => "Booking cannot be cancelled in status: {$hotelBooking->booking_status}."], 400);
         }

         // Example: Prevent cancellation within X days of check-in (adjust logic/rule as needed)
         // $cancellationDeadlineDays = 3;
         // if ($daysUntilCheckIn < $cancellationDeadlineDays) {
         //      return response()->json(['message' => "Booking cannot be cancelled within {$cancellationDeadlineDays} days of check-in."], 400);
         // }

         DB::beginTransaction();

         try {
             // Update booking status to CancelledByUser
             $hotelBooking->update(['booking_status' => 'CancelledByUser']);

             // Optional: Implement refund logic here or trigger a refund process

             DB::commit(); // Commit transaction

             // Return a success response
             return response()->json(['message' => 'Booking cancelled successfully.'], 200); // Using 200 with message

         } catch (\Exception $e) {
              DB::rollBack(); // Rollback transaction
              Log::error('Error cancelling hotel booking: ' . $e->getMessage(), ['booking_id' => $hotelBooking->id, 'user_id' => Auth::id(), 'exception' => $e]);
             return response()->json(['message' => 'Failed to cancel booking. Please try again.'], 500);
         }
    }
}