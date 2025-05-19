<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Rating; // Import Rating model to check for existing rating
use App\Http\Controllers\Api\CommentController; // To reuse the target type mapping helper


class StoreRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // User must be authenticated to create a rating
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Define allowed target types and get the class/table for validation
        // Add/remove types based on what users can rate
        $allowedTargetTypes = ['TouristSite', 'Product', 'Hotel', 'Article', 'SiteExperience']; // Example types

        // Map request type to lowercase for map lookup
        $requestedTargetType = strtolower($this->input('target_type'));

        // Get the model class for the target type using helper from CommentController
        $targetModelClass = (new CommentController())->mapTargetTypeToModel($requestedTargetType);
        $targetTable = $targetModelClass ? (new $targetModelClass)->getTable() : 'some_invalid_table'; // Use a dummy table if model not found


        // Ensure the user has not already rated this specific item
        $userId = Auth::id();


        return [
            'target_type' => ['required', 'string', Rule::in($allowedTargetTypes)], // Must be one of allowed types
            'target_id' => [
                'required',
                'integer',
                // Ensure target_id exists in the corresponding table for the target_type
                 Rule::exists($targetTable, 'id'),
                 // Ensure this user has NOT already rated this specific target
                 Rule::unique('ratings')->where(function ($query) use ($userId) {
                     return $query->where('user_id', $userId)
                                  ->where('target_type', $this->input('target_type')); // Also check target_type
                 }),
            ],
            'rating_value' => ['required', 'integer', 'between:1,5'], // Assuming 1-5 star rating
            'review_title' => ['nullable', 'string', 'max:150'], // Optional title
            'review_text' => ['nullable', 'string'], // Optional review text
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
             'target_type' => __('Item Type'),
             'target_id' => __('Item'),
             'rating_value' => __('Rating'),
             'review_title' => __('Review Title'),
             'review_text' => __('Review Text'),
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
            'target_type.required' => 'يجب تحديد نوع العنصر الذي تقيمه.',
            'target_type.in' => 'نوع العنصر المحدد غير صالح.',
            'target_id.required' => 'يجب تحديد العنصر الذي تقيمه.',
            'target_id.exists' => 'العنصر المحدد للتقييم غير موجود.',
            'target_id.unique' => 'لقد قمت بتقييم هذا العنصر بالفعل.', // Custom message for unique rule
            'rating_value.required' => 'قيمة التقييم مطلوبة.',
            'rating_value.integer' => 'قيمة التقييم يجب أن تكون رقماً صحيحاً.',
            'rating_value.between' => 'قيمة التقييم يجب أن تكون بين :min و :max.',
            // ... add messages for other rules
        ];
    }
}