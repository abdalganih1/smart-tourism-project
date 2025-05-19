<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Not strictly needed in Request, but common to import

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // User must be authenticated to create a comment
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
        $allowedTargetTypes = ['Article', 'Product', 'TouristSite', 'Hotel', 'SiteExperience'];
        // Map request type to lowercase for map lookup
        $requestedTargetType = strtolower($this->input('target_type'));

        // Get the model class for the target type
        $targetModelClass = $this->mapTargetTypeToModel($requestedTargetType);
        $targetTable = $targetModelClass ? (new $targetModelClass)->getTable() : null;


        return [
            'target_type' => ['required', 'string', Rule::in($allowedTargetTypes)], // Must be one of allowed types
            'target_id' => [
                'required',
                'integer',
                // Ensure target_id exists in the corresponding table for the target_type
                 Rule::exists($targetTable ?? 'some_invalid_table', 'id'), // Use a dummy table if model not found
            ],
            'content' => ['required', 'string'], // Comment content is required
            'parent_comment_id' => [
                'nullable',
                'integer',
                // If parent_comment_id is provided, it must exist and belong to the same target
                Rule::exists('comments', 'id')->where(function ($query) {
                    $query->where('target_type', $this->input('target_type'))
                          ->where('target_id', $this->input('target_id'));
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
             'target_type' => __('Target Type'),
             'target_id' => __('Target ID'),
             'content' => __('Comment Content'),
             'parent_comment_id' => __('Parent Comment'),
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
            'target_type.required' => 'يجب تحديد نوع العنصر الذي تعلق عليه.',
            'target_type.in' => 'نوع العنصر المحدد غير صالح.',
            'target_id.required' => 'يجب تحديد العنصر الذي تعلق عليه.',
            'target_id.integer' => 'معرف العنصر يجب أن يكون رقماً صحيحاً.',
            'target_id.exists' => 'العنصر المحدد للتعليق عليه غير موجود.',
            'content.required' => 'محتوى التعليق مطلوب.',
            'parent_comment_id.exists' => 'التعليق الأب المحدد غير موجود أو لا ينتمي لنفس العنصر الهدف.',
            // ... add messages for other rules
        ];
    }

     /**
      * Helper method to map target_type string to Model class (Duplicate of controller helper).
      * Consider putting this in a shared trait or helper class if used frequently.
      *
      * @param string $targetType
      * @return string|null The full model class name or null if not found.
      */
     protected function mapTargetTypeToModel(string $targetType): ?string
     {
         $map = [
             'article' => \App\Models\Article::class,
             'product' => \App\Models\Product::class,
             'touristsite' => \App\Models\TouristSite::class,
             'hotel' => \App\Models\Hotel::class,
             'siteexperience' => \App\Models\SiteExperience::class,
             // Add other polymorphic targets here
         ];

         return $map[strtolower($targetType)] ?? null;
     }
}