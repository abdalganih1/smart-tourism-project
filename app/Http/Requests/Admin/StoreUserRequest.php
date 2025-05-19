<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Import Rule facade
use Illuminate\Support\Facades\Auth; // Required if you need to check authenticated user

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // In an admin panel protected by middleware/gates,
        // we assume that if a user can reach this point, they are authorized.
        // Authorization logic based on user roles/permissions should be handled
        // in the route middleware (e.g., 'can:access-admin-panel') or policies,
        // not typically here for basic form requests within a protected area.
        // So, we return true if the user is authenticated (as a safety check, though middleware does this).
        return Auth::check(); // Return true if *any* user is authenticated.
                              // The 'can:access-admin-panel' middleware ensures *only* admins get here.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Define the allowed user types based on your schema
        $allowedUserTypes = ['Tourist', 'Vendor', 'HotelBookingManager', 'ArticleWriter', 'Employee', 'Admin'];

        return [
            // User Account Information
            'username' => ['required', 'string', 'max:100', 'unique:users'], // Username must be unique
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'], // Email must be unique
            'password' => ['required', 'string', 'min:8', 'confirmed'], // Password required, min 8 chars, must match password_confirmation
            'password_confirmation' => ['required', 'string', 'min:8'], // Password confirmation required
            'user_type' => ['required', 'string', Rule::in($allowedUserTypes)], // User type must be one of the allowed types
            'is_active' => ['required', 'boolean'], // is_active should be boolean (comes as 0/1 from select)

            // User Profile Information (assuming these are required when creating a user)
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'father_name' => ['nullable', 'string', 'max:100'], // Nullable as per schema
            'mother_name' => ['nullable', 'string', 'max:100'], // Nullable as per schema
            'bio' => ['nullable', 'string'], // Nullable as per schema

            // Validation for File Uploads (Example - uncomment and adjust if you add file fields to the form)
            // 'passport_image' => ['nullable', 'image', 'max:2048'], // Max 2MB, image types
            // 'profile_picture' => ['nullable', 'image', 'max:2048'],

            // Validation for Phone Numbers (Example - uncomment and adjust if you add phone fields)
            // Assumes 'phone_numbers' is an array of objects/arrays in the request
            // 'phone_numbers' => ['nullable', 'array'],
            // 'phone_numbers.*.phone_number' => ['required', 'string', 'max:30'], // Basic phone number validation
            // 'phone_numbers.*.is_primary' => ['nullable', 'boolean'],
            // 'phone_numbers.*.description' => ['nullable', 'string', 'max:100'],
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
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.unique' => 'اسم المستخدم هذا مستخدم بالفعل.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.',
            'password.confirmed' => 'كلمة المرور وتأكيدها غير متطابقين.',
            'password_confirmation.required' => 'تأكيد كلمة المرور مطلوب.',
            'user_type.required' => 'نوع المستخدم مطلوب.',
            'user_type.in' => 'نوع المستخدم المحدد غير صالح.',
            'is_active.required' => 'حالة النشاط مطلوبة.',
            'is_active.boolean' => 'حالة النشاط يجب أن تكون قيمة منطقية.',
            'first_name.required' => 'الاسم الأول مطلوب.',
            'last_name.required' => 'اسم العائلة مطلوب.',
            // Add messages for other fields
        ];
    }
}