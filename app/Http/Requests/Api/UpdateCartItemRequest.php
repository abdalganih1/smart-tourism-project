<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\ShoppingCartItem; // Import model to access route model bound item

class UpdateCartItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // User must be authenticated to update a cart item
        if (!Auth::check()) {
            return false;
        }

        // Get the cart item model instance being updated via route model binding
        // The route parameter name is 'cartItem' by default for a resource controller
        $cartItem = $this->route('cartItem');

        // User must own the cart item to update it
        return $cartItem && $cartItem->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the cart item instance to check product stock
        $cartItem = $this->route('cartItem');
        $product = $cartItem ? $cartItem->product : null; // Load product relation if not already


        return [
            'quantity' => [
                'required',
                'integer',
                'min:1', // Quantity must be at least 1
                 // Ensure quantity does not exceed available stock
                 Rule::when($product && $product->exists, function ($rule) use ($product) {
                      $rule->max($product->stock_quantity)->message('الكمية المطلوبة للمنتج :attribute تتجاوز المخزون المتاح (:max).');
                 }),
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
             'quantity' => __('Quantity'),
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
            'quantity.required' => 'الكمية مطلوبة.',
            'quantity.integer' => 'الكمية يجب أن تكون رقماً صحيحاً.',
            'quantity.min' => 'يجب أن لا تقل الكمية عن :min.',
             // Custom max message is defined in the rule itself
            // 'quantity.max' => 'الكمية المطلوبة تتجاوز المخزون المتاح.',
            // ...
        ];
    }
}