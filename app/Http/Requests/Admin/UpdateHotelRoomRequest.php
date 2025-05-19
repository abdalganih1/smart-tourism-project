<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateHotelRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin, Employee, or HotelBookingManager can update rooms
        // Add policy check here: $this->user()->can('update', $this->route('hotel_room'))
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee', 'HotelBookingManager']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the room ID from the route parameters to ignore it during unique check
        $roomId = $this->route('hotel_room')->id;

        // Get the potentially new hotel_id from the request input
        $newHotelId = $this->input('hotel_id');


        return [
            'hotel_id' => ['required', 'integer', Rule::exists('hotels', 'id')],
            'room_type_id' => ['required', 'integer', Rule::exists('hotel_room_types', 'id')],
            'room_number' => [
                'required',
                'string',
                'max:20',
                 // Ensure room number is unique within the NEWLY specified hotel_id, ignoring the current room
                 Rule::unique('hotel_rooms', 'room_number')
                    ->where('hotel_id', $newHotelId)
                    ->ignore($roomId),
            ],
            'price_per_night' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'area_sqm' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'max_occupancy' => ['nullable', 'integer', 'min:1'],
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
            'room_number.unique' => 'رقم الغرفة هذا موجود بالفعل في الفندق المحدد.',
            'price_per_night.required' => 'سعر الليلة مطلوب.',
            'price_per_night.numeric' => 'سعر الليلة يجب أن يكون رقماً.',
            'price_per_night.min' => 'سعر الليلة يجب أن لا يقل عن 0.',
            'max_occupancy.min' => 'الحد الأقصى للإشغال يجب أن لا يقل عن 1.',
            // ... add messages for other rules
        ];
    }
}