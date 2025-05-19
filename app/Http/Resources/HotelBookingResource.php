<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Import nested resources
// use App\Http\Resources\UserResource; // For user
// use App\Http\Resources\HotelRoomResource; // For room (which can nest HotelResource and HotelRoomTypeResource)


class HotelBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 'resource' property is the underlying HotelBooking model instance
        return [
            'id' => $this->id,
            'user_id' => $this->user_id, // Include FKs or the nested resources
            'room_id' => $this->room_id,
            'check_in_date' => $this->check_in_date->format('Y-m-d'), // Format dates
            'check_out_date' => $this->check_out_date->format('Y-m-d'), // Format dates
            'num_adults' => $this->num_adults,
            'num_children' => $this->num_children,
            'total_amount' => number_format($this->total_amount, 2), // Format currency
            'booking_status' => $this->booking_status,
            'payment_status' => $this->payment_status,
            'payment_transaction_id' => $this->payment_transaction_id,
            'booked_at' => $this->booked_at, // Will be cast to string/format
            'special_requests' => $this->special_requests,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Include relationships if they were loaded
            'user' => new UserResource($this->whenLoaded('user')), // Include user who made the booking
            'room' => new HotelRoomResource($this->whenLoaded('room')), // Include room details (which should nest hotel/type)
        ];
    }
}