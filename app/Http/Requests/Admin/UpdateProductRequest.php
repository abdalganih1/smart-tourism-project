<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Import Rule facade
use Illuminate\Support\Facades\Auth; // For authorization check

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only Admin or Vendor can update products.
        // Additionally, if user is a Vendor, they should only update THEIR OWN products.
        // The 'can' middleware with a Policy would be ideal here (e.g., can('update', $product)).
        // For simplicity in Request, we just check user type. Granular check goes in Controller or Policy.
        return Auth::check() && in_array(Auth::user()->user_type, ['Admin', 'Vendor']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         // Get the product ID from the route parameters
        // $productId = $this->route('product')->id; // Not directly needed for unique ignore here

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
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            // Image upload is optional. If present, it's a file.
            'main_image' => ['nullable', 'image', 'max:2048'],
            // Add a rule to validate 'remove_main_image' checkbox
            'remove_main_image' => ['nullable', 'boolean'], // Expect 0 or 1 from checkbox
            'category_id' => ['nullable', 'integer', Rule::exists('product_categories', 'id')],
            'is_available' => ['required', 'boolean'],
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
             'seller_user_id' => __('Seller'),
             'name' => __('Product Name'),
             'description' => __('Description'),
             'color' => __('Color'),
             'stock_quantity' => __('Stock Quantity'),
             'price' => __('Price'),
             'main_image' => __('Main Image'),
             'remove_main_image' => __('Remove Main Image'),
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
        // Add specific messages here
        return [
            'seller_user_id.required' => 'يجب تحديد البائع لهذا المنتج.',
            'seller_user_id.exists' => 'البائع المحدد غير صالح.',
            'name.required' => 'اسم المنتج مطلوب.',
            // ... add messages for other rules
        ];
    }
}