<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateHotelRoomTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Employee can update room types
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the room type ID from the route parameters to ignore it during unique check
        $roomTypeId = $this->route('hotel_room_type')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('hotel_room_types', 'name')->ignore($roomTypeId), // Ignore the current room type
            ],
            'description' => ['nullable', 'string'],
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
             'name' => __('Room Type Name'),
             'description' => __('Description'),
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
            'name.required' => 'اسم نوع الغرفة مطلوب.',
            'name.unique' => 'اسم نوع الغرفة هذا موجود بالفعل.',
            // ... add messages for other rules
        ];
    }
}