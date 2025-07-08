<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization logic should primarily be handled by the Policy/Gate
        // on the Product model in the controller action (e.g., $this->authorize('update', $product)).
        // This request only ensures *a* user is logged in and could potentially manage products.
        // The specific product ownership check is best in the Policy/Gate.
        $user = Auth::user();
         return $user && ($user->isVendor() || $user->isAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Get the product being updated via route model binding
        $productId = $this->route('product')->id; // Assumes Route Model Binding for 'product'

        return [
            // 'required' becomes 'sometimes' if field is optional on update, but these seem required
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'color' => ['nullable', 'string', 'max:50'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0.01'],
            // Image validation: nullable for file input, but required if needed
            // If a file is uploaded, validate it. If not, keep the old image.
            'main_image' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,svg'],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'is_available' => ['nullable', 'boolean'], // checkbox
        ];
    }
     // Add messages() if needed
}