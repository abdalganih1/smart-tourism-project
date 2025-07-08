<?php

namespace App\Http\Requests\TouristActivity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateTouristActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Policy/Gate should handle if the user can update this specific activity.
        // $this->route('tourist_activity') will provide the model instance if Route Model Binding works.
        $user = Auth::user();
         return $user && ($user->isAdmin() || $user->isEmployee() || $user->isArticleWriter()); // Example: Allow certain roles
         // Or check a specific Gate/Policy: return $user->can('update', $this->route('tourist_activity'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
         // Use 'sometimes|required' for fields that are optional on update requests
        return [
            'name' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'site_id' => ['nullable', 'exists:tourist_sites,id'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'start_datetime' => ['sometimes', 'required', 'date_format:Y-m-d H:i:s', 'after_or_equal:now'], // Example format and check
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'organizer_user_id' => ['nullable', 'exists:users,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
             // Add validation for image/video file updates if applicable
        ];
    }
     // Add prepareForValidation() if needed for date/time combination
     // Add messages() if needed
}