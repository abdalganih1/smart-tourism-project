<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // For date comparisons

class StoreHotelBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // User must be authenticated to create a booking
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $checkInDate = $this->input('check_in_date');

        return [
            'room_id' => ['required', 'integer', Rule::exists('hotel_rooms', 'id')], // Room must exist
            'check_in_date' => ['required', 'date', 'after_or_equal:today'], // Check-in date required, valid date, not in the past
            'check_out_date' => ['required', 'date', 'after:check_in_date'], // Check-out date required, valid date, must be after check-in date
            'num_adults' => ['required', 'integer', 'min:1'], // At least one adult required
            'num_children' => ['nullable', 'integer', 'min:0'], // Children is optional, must be 0 or more
            'special_requests' => ['nullable', 'string'], // Optional special requests
            // Note: Payment details (like transaction ID) are usually handled after booking creation in a payment process
            // 'payment_transaction_id' => ['nullable', 'string', 'unique:hotel_bookings,payment_transaction_id'], // If you save transaction ID here
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
             'room_id' => __('Room'),
             'check_in_date' => __('Check-in Date'),
             'check_out_date' => __('Check-out Date'),
             'num_adults' => __('Number of Adults'),
             'num_children' => __('Number of Children'),
             'special_requests' => __('Special Requests'),
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
            'room_id.required' => 'يجب تحديد الغرفة.',
            'room_id.exists' => 'الغرفة المحددة غير موجودة.',
            'check_in_date.required' => 'تاريخ تسجيل الدخول مطلوب.',
            'check_in_date.date' => 'تاريخ تسجيل الدخول يجب أن يكون تاريخاً صالحاً.',
            'check_in_date.after_or_equal' => 'تاريخ تسجيل الدخول لا يمكن أن يكون في الماضي.',
            'check_out_date.required' => 'تاريخ تسجيل الخروج مطلوب.',
            'check_out_date.date' => 'تاريخ تسجيل الخروج يجب أن يكون تاريخاً صالحاً.',
            'check_out_date.after' => 'تاريخ تسجيل الخروج يجب أن يكون بعد تاريخ تسجيل الدخول.',
            'num_adults.required' => 'عدد البالغين مطلوب.',
            'num_adults.integer' => 'عدد البالغين يجب أن يكون رقماً صحيحاً.',
            'num_adults.min' => 'يجب أن لا يقل عدد البالغين عن :min.',
            'num_children.integer' => 'عدد الأطفال يجب أن يكون رقماً صحيحاً.',
            'num_children.min' => 'يجب أن لا يقل عدد الأطفال عن :min.',
            // ...
        ];
    }

     /**
      * Prepare the data for validation.
      *
      * @return void
      */
     protected function prepareForValidation(): void
     {
         // Cast date strings to Carbon instances if needed before validation (less common here)
         // Ensure num_children is set to 0 if null or empty
         if (!$this->filled('num_children')) {
             $this->merge(['num_children' => 0]);
         }
     }
}