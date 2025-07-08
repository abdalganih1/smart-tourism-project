<?php

namespace App\Http\Requests\TouristActivity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreTouristActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only Admins or authorized Employees/Users can create activities via Admin panel
        $user = Auth::user();
        return $user && ($user->isAdmin() || $user->isEmployee() || $user->isArticleWriter()); // Example: Allow certain roles
         // Or check a specific Gate/Policy: return $user->can('create', \App\Models\TouristActivity::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'site_id' => ['nullable', 'exists:tourist_sites,id'], // Optional link to a site
            'location_text' => ['nullable', 'string', 'max:255'], // Alternative location if not linked to site
             // Ensure either site_id or location_text is present if needed
            // 'start_datetime' format should match database timestamp/datetime format
            'start_datetime' => ['required', 'date_format:Y-m-d H:i:s', 'after_or_equal:now'], // Example format and check
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'organizer_user_id' => ['nullable', 'exists:users,id'], // Link to a user who organizes (Vendor, Employee?)
            'price' => ['nullable', 'numeric', 'min:0'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            // Add validation for image/video files if schema includes them for activities
        ];
    }

     /**
     * Prepare the data for validation.
     * Add data here if needed before validation.
     */
    protected function prepareForValidation()
    {
        // If you have date and time inputs separately in the form, combine them here
        // Example: assuming 'start_date' and 'start_time' are form inputs
        // if ($this->has('start_date') && $this->has('start_time')) {
        //     $startDatetime = $this->input('start_date') . ' ' . $this->input('start_time') . ':00';
        //     $this->merge(['start_datetime' => $startDatetime]);
        // }
    }
    // Add messages() if needed
}