<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreTouristSiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Employee can create tourist sites
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Define allowed user types for site managers
        $allowedManagerTypes = ['Admin', 'Employee'];

        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'location_text' => ['nullable', 'string', 'max:255'],
            // Latitude/Longitude validation: numeric, within a plausible range
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'], // Allow null, default 'Syria' handled in controller/migration
            'category_id' => ['nullable', 'integer', Rule::exists('site_categories', 'id')], // Optional category, must exist if provided
            'main_image' => ['nullable', 'image', 'max:5120'], // Optional image upload, max 5MB (adjust as needed), image types
            'video_url' => ['nullable', 'string', 'url', 'max:255'], // Optional video URL, must be a valid URL if provided, max 255 chars
            'added_by_user_id' => [
                'nullable', // Allow null if Admin is automatically assigned
                'integer',
                // Ensure added_by_user_id exists in users table and has an allowed type, if provided
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
             'name' => __('Site Name'),
             'description' => __('Description'),
             'location_text' => __('Location Text'),
             'latitude' => __('Latitude'),
             'longitude' => __('Longitude'),
             'city' => __('City'),
             'country' => __('Country'),
             'category_id' => __('Category'),
             'main_image' => __('Main Image'),
             'video_url' => __('Video URL'),
             'added_by_user_id' => __('Added By User'),
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
            'name.required' => 'اسم الموقع مطلوب.',
            'description.required' => 'وصف الموقع مطلوب.',
            'category_id.exists' => 'الفئة المحددة غير صالحة.',
            'added_by_user_id.exists' => 'المستخدم المحدد كـ "أضيف بواسطة" غير صالح.',
            'latitude.numeric' => 'خط العرض يجب أن يكون رقماً.',
            'longitude.numeric' => 'خط الطول يجب أن يكون رقماً.',
            'latitude.between' => 'خط العرض يجب أن يكون بين -90 و 90.',
            'longitude.between' => 'خط الطول يجب أن يكون بين -180 و 180.',
            'main_image.image' => 'الصورة الرئيسية يجب أن تكون ملف صورة.',
            'main_image.max' => 'حجم الصورة الرئيسية لا يجب أن يتجاوز 5 ميجابايت.',
            'video_url.url' => 'رابط الفيديو يجب أن يكون رابطاً صالحاً.',
            // ... add messages for other rules
        ];
    }
}