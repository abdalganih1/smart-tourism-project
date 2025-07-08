<?php

namespace App\Http\Requests\HotelBooking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\HotelRoom; // Needed for custom validation logic

class StoreHotelBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only HotelBookingManagers or Admins can create bookings via this panel
        $user = Auth::user();
         return $user && ($user->isHotelBookingManager() || $user->isAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'], // User being booked for must exist
            'room_id' => [
                 'required',
                 'exists:hotel_rooms,id',
                 // Custom validation to ensure the room belongs to a hotel managed by the current user
                 Rule::exists('hotel_rooms', 'id')->where(function ($query) {
                     $user = Auth::user();
                     $managedHotelIds = $user->hotelsManaged->pluck('id');
                     return $query->whereIn('hotel_id', $managedHotelIds);
                 })
             ],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'], // Check-in date must be today or in future
            'check_out_date' => ['required', 'date', 'after:check_in_date'], // Check-out date must be after check-in
            'num_adults' => ['required', 'integer', 'min:1'],
            'num_children' => ['nullable', 'integer', 'min:0'],
            // total_amount is typically calculated, not submitted by form
            // 'booking_status' and 'payment_status' would have defaults set in controller/migration
            'special_requests' => ['nullable', 'string'],
        ];
    }

    /**
     * Prepare the data for validation.
     * Add data here if needed before validation (e.g., calculate total_amount).
     */
    protected function prepareForValidation()
    {
         // Calculate total_amount here if you expect the frontend to send dates and room_id
         // And you want to validate or set the total based on backend calculation.
         // This requires fetching the room and dates.
         if ($this->filled(['room_id', 'check_in_date', 'check_out_date'])) {
             $room = HotelRoom::find($this->room_id);
             $checkIn = \Carbon\Carbon::parse($this->check_in_date);
             $checkOut = \Carbon\Carbon::parse($this->check_out_date);

             if ($room && $checkIn->isValid() && $checkOut->isValid() && $checkOut->greaterThan($checkIn)) {
                 $numberOfNights = $checkIn->diffInDays($checkOut);
                 $totalAmount = $room->price_per_night * $numberOfNights;
                 $this->merge(['total_amount' => $totalAmount]); // Add calculated total_amount to request data
             }
         }
         // Ensure default statuses are merged if not provided by form (or set in controller after validation)
         // $this->merge(['booking_status' => $this->booking_status ?? 'PendingConfirmation']);
         // $this->merge(['payment_status' => $this->payment_status ?? 'Unpaid']);
    }

    // Add messages() if needed
}