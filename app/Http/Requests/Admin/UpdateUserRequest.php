<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Import Rule facade
use Illuminate\Support\Facades\Auth; // Required for authorization check

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Authorization should ideally be handled by middleware or policies.
        // Returning true here assumes the user has already passed
        // the 'can:access-admin-panel' middleware on the route group.
        // A basic check that *any* user is authenticated is a minimum.
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the user ID from the route parameters using route() helper.
        // This is necessary for ignoring the current user during unique checks.
        $userId = $this->route('user')->id;

        // Define the allowed user types based on your schema.
        $allowedUserTypes = ['Tourist', 'Vendor', 'HotelBookingManager', 'ArticleWriter', 'Employee', 'Admin'];

        return [
            // User Account Information
            'username' => [
                'required',
                'string',
                'max:100',
                // Ensure username is unique, ignoring the user being updated.
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                // Ensure email is unique, ignoring the user being updated.
                Rule::unique('users', 'email')->ignore($userId),
            ],

            // Password Field Validation:
            // 1. It's optional (nullable).
            // 2. If it's NOT empty ($this->filled('password')), then apply:
            //    - Must be a string.
            //    - Minimum length of 8.
            //    - Must match the 'password_confirmation' field ('confirmed' rule).
            'password' => [
                'nullable', // Allow null or empty string
                Rule::when($this->filled('password'), ['string', 'min:8', 'confirmed']),
                // Note: The 'string' rule is applied conditionally here. If the field
                // is not filled, this conditional rule set is skipped entirely.
            ],

            // Password Confirmation Field Validation:
            // 1. It's required ONLY IF the 'password' field is filled.
            // 2. If it's NOT empty ($this->filled('password_confirmation')), then apply:
            //    - Must be a string.
            //    - Minimum length of 8.
            //    - (The 'confirmed' rule on the 'password' field handles the match check implicitly).
            'password_confirmation' => [
                 // Make this field required only if the 'password' field is filled.
                 Rule::requiredIf($this->filled('password')),
                 // Apply 'string' and 'min:8' rules only when the 'password_confirmation' field is NOT empty.
                 Rule::when($this->filled('password_confirmation'), ['string', 'min:8']),
                 // Note: If password field is NOT filled, requiredIf is false, so password_confirmation is not required.
                 // If password field IS filled, requiredIf is true, so password_confirmation IS required.
                 // In that case, if password_confirmation is EMPTY (''), filled() for it is false, so Rule::when is skipped.
                 // This covers the case where password_confirmation is required but empty.
            ],


            'user_type' => ['required', 'string', Rule::in($allowedUserTypes)],
            'is_active' => ['required', 'boolean'],

            // User Profile Information (assuming these are required for update based on schema/form)
            // These fields are on the UserProfile table, but the form sends them directly.
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'father_name' => ['nullable', 'string', 'max:100'], // Nullable as per schema
            'mother_name' => ['nullable', 'string', 'max:100'], // Nullable as per schema
            'bio' => ['nullable', 'string'], // Nullable as per schema

             // Validation for File Uploads (Example - uncomment and adjust if you add file fields to the form)
             // 'passport_image' => ['nullable', 'image', 'max:2048'], // Allow image types up to 2MB
             // 'profile_picture' => ['nullable', 'image', 'max:2048'],

            // Validation for Phone Numbers (Example - uncomment and adjust based on your form's structure)
            // If you send an array of phone numbers to update:
            // 'phone_numbers' => ['nullable', 'array'],
            // 'phone_numbers.*.id' => ['nullable', 'integer', Rule::exists('user_phone_numbers', 'id')->where('user_id', $userId)], // Validate existing phone IDs belong to the user
            // 'phone_numbers.*.phone_number' => ['required', 'string', 'max:30'],
            // 'phone_numbers.*.is_primary' => ['nullable', 'boolean'],
            // 'phone_numbers.*.description' => ['nullable', 'string', 'max:100'],
            // If you send an array of IDs to delete:
            // 'phone_numbers_to_delete' => ['nullable', 'array'],
            // 'phone_numbers_to_delete.*' => ['integer', Rule::exists('user_phone_numbers', 'id')->where('user_id', $userId)], // Validate phone IDs to delete belong to the user

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
            // User Account Validation Messages
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.unique' => 'اسم المستخدم هذا مستخدم بالفعل من قبل مستخدم آخر.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل من قبل مستخدم آخر.',

            // Password Validation Messages (Messages for rules applied to the 'password' field)
            'password.min' => 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل إذا تم إدخالها.',
            'password.confirmed' => 'كلمة المرور وتأكيدها غير متطابقين.', // This message comes from the 'confirmed' rule applied to 'password'

            // Password Confirmation Validation Messages (Messages for rules applied to 'password_confirmation' field)
            'password_confirmation.required_if' => 'تأكيد كلمة المرور مطلوب عند تغيير كلمة المرور.', // Message for Rule::requiredIf
            'password_confirmation.string' => 'تأكيد كلمة المرور يجب أن يكون نصاً.', // Message for the 'string' rule on password_confirmation
            'password_confirmation.min' => 'يجب أن يتكون تأكيد كلمة المرور من 8 أحرف على الأقل.', // Message for the 'min:8' rule on password_confirmation


            'user_type.required' => 'نوع المستخدم مطلوب.',
            'user_type.in' => 'نوع المستخدم المحدد غير صالح.',

            'is_active.required' => 'حالة النشاط مطلوبة.',
            'is_active.boolean' => 'حالة النشاط يجب أن تكون قيمة منطقية.',

            // User Profile Validation Messages
            'first_name.required' => 'الاسم الأول مطلوب.',
            'last_name.required' => 'اسم العائلة مطلوب.',
            // Add messages for other profile fields, files, and phone numbers if you uncomment their rules
        ];
    }
}