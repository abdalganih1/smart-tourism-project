<?php

namespace App\Http\Requests\Hotel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateHotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Policy/Gate should handle if the user manages this specific hotel
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
        $hotelId = $this->route('hotel')->id; // Get hotel ID from route

        return [
            // Assuming these fields are updateable by hotel manager
            'name' => ['required', 'string', 'max:150'],
            'star_rating' => ['nullable', 'integer', 'min:1', 'max:7'],
            'description' => ['nullable', 'string'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'string', 'email', 'max:100'],
            'main_image' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,svg'],
            'latitude' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            // 'managed_by_user_id' should NOT be in update request from manager
        ];
    }
     // Add messages() if needed
}