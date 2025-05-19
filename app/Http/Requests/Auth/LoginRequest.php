<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException; // Import ValidationException
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Hash; // Import Hash facade
use App\Models\User; // Import User model

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assuming login is public and anyone can attempt to log in
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
            // 'login' field can be username or email
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
             // Add rules for device_name if sent by client
            'device_name' => ['nullable', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     * This method is not strictly necessary if you handle authentication in the controller
     * as done in AuthController@login, but can be useful for direct usage with Auth::attempt.
     * Keeping the logic in the controller as you did is perfectly fine for API.
     * If you WERE using Auth::attempt, you might add logic here like:
     */
    // public function authenticate(): void
    // {
    //     // Find the user by username or email
    //     $user = User::where('username', $this->login)
    //                 ->orWhere('email', $this->login)
    //                 ->first();

    //      // Use attempt() on the found user if you loaded the Authenticatable trait correctly
    //      // Note: Auth::attempt typically expects 'email'/'password' by default.
    //      // If using username or email interchangeably, and password_hash column,
    //      // the manual check in the controller is simpler than customizing Auth::attempt.
    //      // Stick to the controller's logic for now.

    //     // This method is primarily for web guard; for API using Sanctum tokens,
    //     // the check happens after getting the token, typically not here.
    //     // So, leaving this empty or removing is fine for your API use case.
    // }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'login.required' => 'اسم المستخدم أو البريد الإلكتروني مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ];
    }
}