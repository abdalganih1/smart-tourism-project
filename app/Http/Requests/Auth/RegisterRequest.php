<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Import Rule facade

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assuming registration is public and anyone can register
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:100', 'unique:users'], // Must be unique in 'users' table
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'], // Must be unique email
            'password' => ['required', 'string', 'min:8', 'confirmed'], // Requires 'password_confirmation' field, min 8 chars
            'user_type' => ['nullable', 'string', Rule::in(['Tourist', 'Vendor'])], // Allow 'Tourist' or 'Vendor', nullable for default
            // Add rules for UserProfile fields if collected during registration
            'first_name' => ['required', 'string', 'max:100'], // Assuming first_name is required for profile
            'last_name' => ['required', 'string', 'max:100'], // Assuming last_name is required for profile
            // 'father_name' => ['nullable', 'string', 'max:100'],
            // 'mother_name' => ['nullable', 'string', 'max:100'],
            // Add rules for device_name if sent by client
            'device_name' => ['nullable', 'string'],
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
            'username.unique' => 'اسم المستخدم هذا موجود بالفعل.',
            'email.unique' => 'هذا البريد الإلكتروني مسجل بالفعل.',
            'password.min' => 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.',
            'password.confirmed' => 'كلمة المرور وتأكيدها غير متطابقين.',
            'user_type.in' => 'نوع المستخدم المحدد غير صالح.',
            // Add custom messages for other rules if needed
        ];
    }
}