<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\TouristSite; // Import TouristSite model to validate site_id
use Illuminate\Support\Facades\Auth;

class StoreSiteExperienceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // User must be authenticated to create a site experience
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'site_id' => ['required', 'integer', Rule::exists('tourist_sites', 'id')], // Site must exist
            'title' => ['nullable', 'string', 'max:200'], // Optional title
            'content' => ['required', 'string'], // Experience content is required
            'visit_date' => ['nullable', 'date'], // Optional visit date
            'photo' => ['nullable', 'image', 'max:5120'], // Optional photo upload, max 5MB, image types
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
             'site_id' => __('Tourist Site'),
             'title' => __('Title'),
             'content' => __('Content'),
             'visit_date' => __('Visit Date'),
             'photo' => __('Photo'),
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
            'site_id.required' => 'يجب تحديد الموقع السياحي.',
            'site_id.exists' => 'الموقع السياحي المحدد غير موجود.',
            'content.required' => 'محتوى التجربة مطلوب.',
            'visit_date.date' => 'تاريخ الزيارة يجب أن يكون تاريخاً صالحاً.',
            'photo.image' => 'الصورة يجب أن تكون ملف صورة.',
            'photo.max' => 'حجم الصورة لا يجب أن يتجاوز 5 ميجابايت.',
            // ...
        ];
    }
}