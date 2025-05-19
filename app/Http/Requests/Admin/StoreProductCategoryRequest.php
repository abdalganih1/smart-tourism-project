<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreProductCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Employee can create product categories
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Employee']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:product_categories'], // Name must be unique
            'description' => ['nullable', 'string'], // Description is optional
            'parent_category_id' => ['nullable', 'integer', Rule::exists('product_categories', 'id')], // Parent must exist if provided
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
            // ... add messages for other rules
        ];
    }
}