<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or ArticleWriter can update articles.
        // Add policy check here: $this->user()->can('update', $this->route('article'))
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'ArticleWriter']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the article ID from the route parameters if needed for unique checks (not needed here)
        // $articleId = $this->route('article')->id;

        // Define allowed user types for authors
        $allowedAuthorTypes = ['Admin', 'ArticleWriter'];
        // Define allowed article statuses
        $allowedStatuses = ['Draft', 'Published', 'Archived'];

        return [
            'author_user_id' => [
                'required',
                'integer',
                 Rule::exists('users', 'id')->where(function ($query) use ($allowedAuthorTypes) {
                     $query->whereIn('user_type', $allowedAuthorTypes);
                }),
            ],
            'title' => ['required', 'string', 'max:250'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string'],
            'tags' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in($allowedStatuses)],
            'published_at' => ['nullable', 'date'],

            // Image upload is optional for update. If present, it's a file.
            'main_image' => ['nullable', 'image', 'max:5120'], // Max 5MB
            // Rule to validate 'remove_main_image' checkbox
            'remove_main_image' => ['nullable', 'boolean'], // Expect 0 or 1 from checkbox

            // Video URL is optional for update. If present, must be a valid URL.
            'video_url' => ['nullable', 'string', 'url', 'max:255'],
            // Add a rule to validate 'remove_video' checkbox (if implemented in form)
            // 'remove_video' => ['nullable', 'boolean'],
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
             'remove_main_image' => __('Remove Main Image'),
             'video_url' => __('Video URL'),
             // 'remove_video' => __('Remove Video'),
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