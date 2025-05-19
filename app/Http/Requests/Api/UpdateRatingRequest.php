<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Required for authorization check

class UpdateRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // User must be authenticated to update a rating
        if (!Auth::check()) {
            return false;
        }

        // Get the rating model instance being updated via route model binding
        // The route parameter name is 'rating' by default for a resource controller
        $rating = $this->route('rating');

        // User must own the rating to update it
        return $rating && $rating->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating_value' => ['required', 'integer', 'between:1,5'], // Rating value is required
            'review_title' => ['nullable', 'string', 'max:150'], // Optional title
            'review_text' => ['nullable', 'string'], // Optional review text
            // Do NOT allow updating target_type, target_id, or user_id via update
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
            'rating_value.required' => 'قيمة التقييم مطلوبة.',
            'rating_value.integer' => 'قيمة التقييم يجب أن تكون رقماً صحيحاً.',
            'rating_value.between' => 'قيمة التقييم يجب أن تكون بين :min و :max.',
            // ...
        ];
    }
}