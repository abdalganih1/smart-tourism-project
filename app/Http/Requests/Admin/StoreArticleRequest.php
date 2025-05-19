<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or ArticleWriter can create articles
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'ArticleWriter']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Define allowed user types for authors
        $allowedAuthorTypes = ['Admin', 'ArticleWriter'];
        // Define allowed article statuses
        $allowedStatuses = ['Draft', 'Published', 'Archived'];


        return [
            'author_user_id' => [
                'required', // Assuming author is required
                'integer',
                // Ensure author_user_id exists in users table and has an allowed type
                Rule::exists('users', 'id')->where(function ($query) use ($allowedAuthorTypes) {
                    $query->whereIn('user_type', $allowedAuthorTypes);
                }),
            ],
            'title' => ['required', 'string', 'max:250'],
            'content' => ['required', 'string'], // Assuming HTML content is allowed
            'excerpt' => ['nullable', 'string'],
            'tags' => ['nullable', 'string', 'max:255'], // Stored as comma-separated string
            'status' => ['required', 'string', Rule::in($allowedStatuses)], // Must be one of the allowed statuses
            'published_at' => ['nullable', 'date'], // Optional date/time

            'main_image' => ['nullable', 'image', 'max:5120'], // Optional image upload, max 5MB
            'video_url' => ['nullable', 'string', 'url', 'max:255'], // Optional video URL
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
             'author_user_id' => __('Author'),
             'title' => __('Title'),
             'content' => __('Content'),
             'excerpt' => __('Excerpt'),
             'tags' => __('Tags'),
             'status' => __('Status'),
             'published_at' => __('Published Date'),
             'main_image' => __('Main Image'),
             'video_url' => __('Video URL'),
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
            'author_user_id.required' => 'يجب تحديد كاتب المقال.',
            'author_user_id.exists' => 'الكاتب المحدد غير صالح.',
            'title.required' => 'عنوان المقال مطلوب.',
            'content.required' => 'محتوى المقال مطلوب.',
            'status.required' => 'حالة المقال مطلوبة.',
            'status.in' => 'حالة المقال المحدد غير صالحة.',
            'published_at.date' => 'تاريخ النشر يجب أن يكون تاريخاً صالحاً.',
            'main_image.image' => 'الصورة الرئيسية يجب أن تكون ملف صورة.',
            'main_image.max' => 'حجم الصورة الرئيسية لا يجب أن يتجاوز 5 ميجابايت.',
            'video_url.url' => 'رابط الفيديو يجب أن يكون رابطاً صالحاً.',
            'video_url.max' => 'رابط الفيديو طويل جداً.',
            // ... add messages for other rules
        ];
    }
}