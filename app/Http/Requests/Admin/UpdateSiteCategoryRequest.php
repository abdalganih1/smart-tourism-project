<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateSiteCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Employee can update site categories
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the category ID from the route parameters to ignore it during unique check
        $categoryId = $this->route('site_category')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('site_categories', 'name')->ignore($categoryId), // Ignore the current category
            ],
            'description' => ['nullable', 'string'],
             // No parent_category_id in SiteCategories as per schema V2.1
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
             'name' => __('Category Name'),
             'description' => __('Description'),
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
            'name.required' => 'اسم الفئة مطلوب.',
            'name.unique' => 'اسم الفئة هذا موجود بالفعل.',
            // ... add messages for other rules
        ];
    }
}