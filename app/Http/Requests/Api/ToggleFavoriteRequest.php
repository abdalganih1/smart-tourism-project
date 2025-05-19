<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ToggleFavoriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // User must be authenticated to toggle a favorite
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Define allowed target types and get the class for validation
        $allowedTargetTypes = ['TouristSite', 'Product', 'Article', 'Hotel', 'SiteExperience']; // Add/remove as per your favorite logic
        // Map request type to lowercase for map lookup
        $requestedTargetType = strtolower($this->input('target_type'));

        // Get the model class for the target type (Reuse mapping helper from CommentController or create a shared one)
        // For simplicity here, inline the mapping check for validation Rule::exists
         $targetTable = null;
         switch ($requestedTargetType) {
             case 'article': $targetTable = 'articles'; break;
             case 'product': $targetTable = 'products'; break;
             case 'touristsite': $targetTable = 'tourist_sites'; break;
             case 'hotel': $targetTable = 'hotels'; break;
             case 'siteexperience': $targetTable = 'site_experiences'; break;
             // Add other cases here
         }


        return [
            'target_type' => ['required', 'string', Rule::in($allowedTargetTypes)], // Must be one of allowed types
            'target_id' => [
                'required',
                'integer',
                // Ensure target_id exists in the corresponding table for the target_type
                 // Use a dummy table name if targetType is invalid to prevent errors, validation will catch target_type.in anyway.
                 Rule::exists($targetTable ?? 'some_invalid_table', 'id'),
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
             'target_type' => __('Target Type'),
             'target_id' => __('Target ID'),
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
            'target_type.required' => 'يجب تحديد نوع العنصر.',
            'target_type.in' => 'نوع العنصر المحدد غير صالح.',
            'target_id.required' => 'يجب تحديد العنصر.',
            'target_id.integer' => 'معرف العنصر يجب أن يكون رقماً صحيحاً.',
            'target_id.exists' => 'العنصر المحدد غير موجود.',
            // ...
        ];
    }
}