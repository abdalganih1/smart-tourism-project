<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Import Rule facade
use Illuminate\Support\Facades\Auth; // For authorization check

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Vendor can create products
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Vendor']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Define allowed user types for the seller
        $allowedSellerTypes = ['Admin', 'Vendor'];

        return [
            'seller_user_id' => [
                'required',
                'integer',
                // Ensure seller_user_id exists in users table and has an allowed type
                Rule::exists('users', 'id')->where(function ($query) use ($allowedSellerTypes) {
                    $query->whereIn('user_type', $allowedSellerTypes);
                }),
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'color' => ['nullable', 'string', 'max:50'],
            'stock_quantity' => ['required', 'integer', 'min:0'], // Stock must be 0 or more
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'], // Price must be numeric with up to 2 decimal places
            'main_image' => ['nullable', 'image', 'max:2048'], // Optional image upload, max 2MB, image types
            'category_id' => ['nullable', 'integer', Rule::exists('product_categories', 'id')], // Optional category, must exist if provided
            'is_available' => ['required', 'boolean'], // must be boolean (comes as 0/1 from select)
        ];
    }

     /**
      * Get custom attributes for validator errors.
      * Useful for providing friendly names in error messages.
      *
      * @return array<string, string>
      */
     public function attributes(): array
     {
         return [
             'seller_user_id' => __('Seller'),
             'name' => __('Product Name'),
             'description' => __('Description'),
             'color' => __('Color'),
             'stock_quantity' => __('Stock Quantity'),
             'price' => __('Price'),
             'main_image' => __('Main Image'),
             'category_id' => __('Category'),
             'is_available' => __('Availability'),
         ];
     }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        // You can add specific messages here if the default ones (using attributes()) are not enough
        return [
            'seller_user_id.required' => 'يجب تحديد البائع لهذا المنتج.',
            'seller_user_id.exists' => 'البائع المحدد غير صالح.',
            'name.required' => 'اسم المنتج مطلوب.',
            // ... add messages for other rules
        ];
    }
}