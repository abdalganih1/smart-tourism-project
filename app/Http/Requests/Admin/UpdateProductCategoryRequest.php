<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateProductCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Employee can update product categories
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
        $categoryId = $this->route('product_category')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('product_categories', 'name')->ignore($categoryId), // Ignore the current category
            ],
            'description' => ['nullable', 'string'],
            'parent_category_id' => [
                'nullable',
                'integer',
                Rule::exists('product_categories', 'id'), // Parent must exist if provided
                 // Rule to prevent a category from being its own parent or being a parent of its own descendant
                 Rule::notIn([$categoryId]), // Cannot be its own parent
                 // More complex check for preventing cycles requires custom logic or a recursive rule
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
             'name' => __('Category Name'),
             'description' => __('Description'),
             'parent_category_id' => __('Parent Category'),
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
            'parent_category_id.exists' => 'الفئة الأب المحددة غير صالحة.',
            'parent_category_id.not_in' => 'لا يمكن أن تكون الفئة هي نفسها الفئة الأب.',
            // ... add messages for other rules
        ];
    }
}