<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // Import Auth Facade for user type check
use Illuminate\Validation\Rule; // Import Rule facade

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only Vendors or Admins can create products
        $user = Auth::user();
        return $user && ($user->isVendor() || $user->isAdmin());
        // Note: For Vendor role, the controller will further ensure the product is linked to THIS vendor.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'color' => ['nullable', 'string', 'max:50'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0.01'], // Price must be positive
            'main_image' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,svg'], // Max 2MB, common image types
            'category_id' => ['nullable', 'exists:product_categories,id'], // Must exist in categories table
            'is_available' => ['nullable', 'boolean'], // Checkbox sends 1/0 or 'on'/null. 'boolean' rule handles this well.
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
            'name.required' => 'اسم المنتج مطلوب.',
            // Add custom messages for other rules as needed
        ];
    }
}