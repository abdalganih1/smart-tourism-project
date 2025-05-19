<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreHotelRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin, Employee, or HotelBookingManager can create rooms
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee', 'HotelBookingManager']);
        // Optional: Add policy check here like $this->user()->can('create', HotelRoom::class)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // When creating, room number must be unique within the selected hotel
        $hotelId = $this->input('hotel_id');

        return [
            'hotel_id' => ['required', 'integer', Rule::exists('hotels', 'id')],
            'room_type_id' => ['required', 'integer', Rule::exists('hotel_room_types', 'id')],
            'room_number' => [
                'required',
                'string',
                'max:20',
                // Rule::unique('hotel_rooms', 'room_number')->where('hotel_id', $hotelId), // Ensures uniqueness within the hotel
                 // More robust check considering the case where hotel_id might change during edit (handled in update request)
            ],
            'price_per_night' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'area_sqm' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'max_occupancy' => ['nullable', 'integer', 'min:1'], // Minimum 1 person occupancy
            'description' => ['nullable', 'string'],
            'is_available_for_booking' => ['required', 'boolean'],
        ];
    }

    /**
      * Get custom attributes for validator errors.
      *
      * @return array<string, string>
      */
     public function attributes(): array
     {
         return [
             'hotel_id' => __('Hotel'),
             'room_type_id' => __('Room Type'),
             'room_number' => __('Room Number'),
             'price_per_night' => __('Price Per Night'),
             'area_sqm' => __('Area (sqm)'),
             'max_occupancy' => __('Maximum Occupancy'),
             'description' => __('Description'),
             'is_available_for_booking' => __('Available for Booking'),
         ];
     }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'hotel_id.required' => 'الفندق مطلوب.',
            'hotel_id.exists' => 'الفندق المحدد غير صالح.',
            'room_type_id.required' => 'نوع الغرفة مطلوب.',
            'room_type_id.exists' => 'نوع الغرفة المحدد غير صالح.',
            'room_number.required' => 'رقم الغرفة مطلوب.',
            // 'room_number.unique' => 'رقم الغرفة هذا موجود بالفعل في هذا الفندق.', // If using unique rule
            'price_per_night.required' => 'سعر الليلة مطلوب.',
            'price_per_night.numeric' => 'سعر الليلة يجب أن يكون رقماً.',
            'price_per_night.min' => 'سعر الليلة يجب أن لا يقل عن 0.',
            'max_occupancy.min' => 'الحد الأقصى للإشغال يجب أن لا يقل عن 1.',
            // ... add messages for other rules
        ];
    }
}