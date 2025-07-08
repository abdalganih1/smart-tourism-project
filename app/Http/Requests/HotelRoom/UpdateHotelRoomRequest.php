<?php

namespace App\Http\Requests\HotelRoom;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateHotelRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         // Policy/Gate should handle if the user manages the hotel this room belongs to
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
        $roomId = $this->route('hotel_room')->id; // Get room ID from route

        return [
            // These fields are likely updateable
            'room_number' => [
                 'required',
                 'string',
                 'max:20',
                 // Ensure room number is unique within the *same hotel* and ignore the current room ID
                 Rule::unique('hotel_rooms', 'room_number')->ignore($roomId)->where(function ($query) {
                     // Assuming the form might send hotel_id if it were editable,
                     // but since it's not, we get the hotel_id from the current room
                     $currentHotelId = $this->route('hotel_room')->hotel_id;
                     return $query->where('hotel_id', $currentHotelId);
                 }),
             ],
            'price_per_night' => ['required', 'numeric', 'min:0.01'],
            'area_sqm' => ['nullable', 'numeric', 'min:0'],
            'max_occupancy' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_available_for_booking' => ['nullable', 'boolean'],
             // 'hotel_id' and 'room_type_id' should NOT be in update request from manager
        ];
    }

    // Add messages() if needed
}