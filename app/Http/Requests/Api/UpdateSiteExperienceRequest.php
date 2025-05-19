<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateSiteExperienceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // User must be authenticated to update a site experience
        if (!Auth::check()) {
            return false;
        }

        // Get the experience model instance being updated via route model binding
        // The route parameter name is 'siteExperience' by default for a resource controller
        $experience = $this->route('siteExperience');

        // User must own the experience to update it
        return $experience && $experience->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // site_id can potentially be updated if you allow users to correct it
            'site_id' => ['nullable', 'integer', Rule::exists('tourist_sites', 'id')], // Site must exist if provided
            'title' => ['nullable', 'string', 'max:200'],
            'content' => ['required', 'string'], // Content is required for update
            'visit_date' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'max:5120'], // Optional new photo upload
            'remove_photo' => ['nullable', 'boolean'], // Checkbox to remove photo
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
             'remove_photo' => __('Remove Photo'),
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
             'site_id.exists' => 'الموقع السياحي المحدد غير موجود.',
            'content.required' => 'محتوى التجربة مطلوب.',
            'visit_date.date' => 'تاريخ الزيارة يجب أن يكون تاريخاً صالحاً.',
            'photo.image' => 'الصورة يجب أن تكون ملف صورة.',
            'photo.max' => 'حجم الصورة لا يجب أن يتجاوز 5 ميجابايت.',
            // ...
        ];
    }
}