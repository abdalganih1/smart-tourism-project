<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateTouristSiteRequest extends FormRequest
{
     /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Employee can update tourist sites.
        // A Policy would be ideal here to check if user can update *this specific* site.
        // For simplicity in Request, check user type. Policy check goes in Controller.
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         // Get the site ID from the route parameters if needed for unique checks (not needed here)
         // $siteId = $this->route('tourist_site')->id;

        // Define allowed user types for site managers
        $allowedManagerTypes = ['Admin', 'Employee'];

        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', Rule::exists('site_categories', 'id')],
            // Image upload is optional for update. If present, it's a file.
            'main_image' => ['nullable', 'image', 'max:5120'], // Max 5MB
            // Rule to validate 'remove_main_image' checkbox
            'remove_main_image' => ['nullable', 'boolean'], // Expect 0 or 1 from checkbox
            // Video URL is optional for update. If present, must be a valid URL.
            'video_url' => ['nullable', 'string', 'url', 'max:255'],
            // Rule to validate 'remove_video' checkbox (if implemented in form)
            // 'remove_video' => ['nullable', 'boolean'],

            'added_by_user_id' => [
                'nullable',
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
             'remove_main_image' => __('Remove Main Image'),
             'video_url' => __('Video URL'),
             // 'remove_video' => __('Remove Video'),
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
            'video_url.max' => 'رابط الفيديو طويل جداً.',
            // ... add messages for other rules
        ];
    }
}