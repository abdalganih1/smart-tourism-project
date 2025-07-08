<?php

namespace App\Http\Requests\HotelBooking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateHotelBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         // هذا التحقق يضمن أن المستخدم هو مدير فندق أو مدير عام يمكنه نظرياً تحديث حجز.
         // التحقق من صلاحية المستخدم على *الحجز المحدد* (هل يدير الفندق الخاص به؟)
         // يجب أن يتم في المتحكم باستخدام Gate أو Policy بعد Route Model Binding.
        $user = Auth::user();
         return $user && ($user->isHotelBookingManager() || $user->isAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // ** استخدام اسم المعامل الصحيح في المسار: 'hotel_booking' **
        $bookingId = $this->route('hotel_booking')->id; // Get the ID of the booking being updated


        return [
            // Fields that can potentially be updated by the manager
            // 'required' means the field *must* be present in the request payload.
            // Use 'sometimes' if the field is optional in the update request.
            'check_in_date' => ['sometimes', 'required', 'date', 'after_or_equal:today'], // Managers might update check-in date?
            'check_out_date' => ['sometimes', 'required', 'date', 'after:check_in_date'],
            'num_adults' => ['sometimes', 'required', 'integer', 'min:1'],
            'num_children' => ['sometimes', 'nullable', 'integer', 'min:0'],

            // Status updates are common for managers - should be required if status is meant to be updated
            'booking_status' => ['sometimes', 'required', 'string', Rule::in(['PendingConfirmation', 'Confirmed', 'CancelledByUser', 'CancelledByHotel', 'Completed', 'NoShow'])],
            'payment_status' => ['sometimes', 'required', 'string', Rule::in(['Unpaid', 'Paid', 'PaymentFailed', 'Refunded'])],

            'payment_transaction_id' => ['nullable', 'string', 'max:100'], // If manager can record transaction ID manually
            'special_requests' => ['nullable', 'string'],

            // User_id and room_id are likely NOT updateable via this form
            // total_amount might be read-only or recalculated - not expected in request payload
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
            // Add custom messages here if needed
        ];
    }
}