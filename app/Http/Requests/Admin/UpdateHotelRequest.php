<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateHotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Employee or HotelBookingManager can update hotels.
        // A Policy would be ideal here to check if user can update *this specific* hotel.
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee', 'HotelBookingManager']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         // Get the hotel ID from the route parameters if needed
         // $hotelId = $this->route('hotel')->id;

        // Define allowed user types for managers
        $allowedManagerTypes = ['Admin', 'HotelBookingManager'];

        return [
            'name' => ['required', 'string', 'max:150'],
            'star_rating' => ['nullable', 'integer', 'between:1,7'],
            'description' => ['nullable', 'string'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'string', 'email', 'max:100'],
            'main_image' => ['nullable', 'image', 'max:5120'],
            // Rule to validate 'remove_main_image' checkbox
            'remove_main_image' => ['nullable', 'boolean'], // Expect 0 or 1 from checkbox
            'managed_by_user_id' => [
                'nullable',
                'integer',
                 Rule::exists('users', 'id')->where(function ($query) use ($allowedManagerTypes) {
                     $query->whereIn('user_type', $allowedManagerTypes);
                }),
            ],
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
             'name' => __('Hotel Name'),
             'star_rating' => __('Star Rating'),
             'description' => __('Description'),
             'address_line1' => __('Address Line 1'),
             'city' => __('City'),
             'country' => __('Country'),
             'latitude' => __('Latitude'),
             'longitude' => __('Longitude'),
             'contact_phone' => __('Contact Phone'),
             'contact_email' => __('Contact Email'),
             'main_image' => __('Main Image'),
             'remove_main_image' => __('Remove Main Image'),
             'managed_by_user_id' => __('Managed By User'),
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
            'name.required' => 'اسم الفندق مطلوب.',
            'star_rating.between' => 'عدد النجوم يجب أن يكون بين :min و :max.',
            'managed_by_user_id.exists' => 'المستخدم المحدد كـ "المدير" غير صالح.',
            'latitude.numeric' => 'خط العرض يجب أن يكون رقماً.',
            'longitude.numeric' => 'خط الطول يجب أن يكون رقماً.',
            'latitude.between' => 'خط العرض يجب أن يكون بين -90 و 90.',
            'longitude.between' => 'خط الطول يجب أن يكون بين -180 و 180.',
            'main_image.image' => 'الصورة الرئيسية يجب أن تكون ملف صورة.',
            'main_image.max' => 'حجم الصورة الرئيسية لا يجب أن يتجاوز 5 ميجابايت.',
             'contact_email.email' => 'صيغة البريد الإلكتروني للتواصل غير صحيحة.',
            // ... add messages for other rules
        ];
    }
}