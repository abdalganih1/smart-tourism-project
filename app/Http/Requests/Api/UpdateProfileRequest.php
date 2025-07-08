<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // User must be authenticated to update their profile
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the authenticated user's ID to ignore their email/username during unique checks (if updating user fields)
        $userId = Auth::id();

        // Note: Profile update API typically updates fields on UserProfile table,
        // but you might also allow updating some fields on the User table (email, username).
        // Adjust rules based on which User fields are allowed to be updated here.
        // The controller example primarily updates UserProfile.

        return [
            // UserProfile Fields (required for profile update based on form/schema)
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'father_name' => ['nullable', 'string', 'max:100'],
            'mother_name' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string'],
            // Optional User Fields (if updateable via profile endpoint)
            // 'username' => [
            //      'nullable', // Allow null or empty string if not changing? Or required if sent?
            //      'string',
            //      'max:100',
            //      Rule::unique('users', 'username')->ignore($userId),
            // ],
            // 'email' => [
            //      'nullable', // Allow null or empty string if not changing? Or required if sent?
            //      'string',
            //      'email',
            //      'max:100',
            //      Rule::unique('users', 'email')->ignore($userId),
            // ],

            // Phone Numbers (complex validation depending on how you structure the request)
            // E.g., if sending an array of phones with IDs and delete flags:
            // 'phone_numbers' => ['nullable', 'array'],
            // 'phone_numbers.*.id' => ['nullable', 'integer', Rule::exists('user_phone_numbers', 'id')->where('user_id', $userId)],
            // 'phone_numbers.*.phone_number' => ['required', 'string', 'max:30'],
            // 'phone_numbers.*.is_primary' => ['nullable', 'boolean'],
            // 'phone_numbers.*.description' => ['nullable', 'string', 'max:100'],
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
             'first_name' => __('First Name'),
             'last_name' => __('Last Name'),
             'father_name' => __('Father\'s Name'),
             'mother_name' => __('Mother\'s Name'),
             'bio' => __('Bio'),
             'profile_picture' => __('Profile Picture'),
             'remove_profile_picture' => __('Remove Profile Picture'),
             // 'username' => __('Username'),
             // 'email' => __('Email'),
             // 'phone_numbers' => __('Phone Numbers'),
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
            'first_name.required' => 'الاسم الأول مطلوب.',
            'last_name.required' => 'اسم العائلة مطلوب.',
            'profile_picture.image' => 'صورة الملف الشخصي يجب أن تكون ملف صورة.',
            'profile_picture.max' => 'حجم صورة الملف الشخصي لا يجب أن يتجاوز 5 ميجابايت.',
            // 'username.unique' => 'اسم المستخدم هذا مستخدم بالفعل.',
            // 'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
            // ...
        ];
    }
}