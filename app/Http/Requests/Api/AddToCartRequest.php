<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Product; // Import Product model
use Illuminate\Support\Facades\Auth;

class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // User must be authenticated to add items to cart
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                // Ensure product exists, is available, and has stock
                Rule::exists('products', 'id')->where(function ($query) {
                    $query->where('is_available', true)->where('stock_quantity', '>', 0);
                }),
            ],
            'quantity' => ['required', 'integer', 'min:1'], // Must add at least 1 item
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
             'product_id' => __('Product'),
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
            'product_id.required' => 'يجب تحديد المنتج.',
            'product_id.exists' => 'المنتج المحدد غير موجود أو غير متاح حالياً.',
            'quantity.required' => 'الكمية مطلوبة.',
            'quantity.integer' => 'الكمية يجب أن تكون رقماً صحيحاً.',
            'quantity.min' => 'يجب إضافة عنصر واحد على الأقل.',
            // ...
        ];
    }
}