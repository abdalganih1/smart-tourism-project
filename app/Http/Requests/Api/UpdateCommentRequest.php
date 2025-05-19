<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // Required for authorization check

class UpdateCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // User must be authenticated to update a comment
        if (!Auth::check()) {
            return false;
        }

        // Get the comment model instance being updated via route model binding
        // The route parameter name is 'comment' by default for a resource controller
        $comment = $this->route('comment');

        // User must own the comment to update it
        return $comment && $comment->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string'], // Only allow updating the content
            // Do NOT allow updating target_type, target_id, or parent_comment_id via update
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
             'content' => __('Comment Content'),
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
            'content.required' => 'محتوى التعليق لا يمكن أن يكون فارغاً عند التعديل.',
            // ...
        ];
    }
}