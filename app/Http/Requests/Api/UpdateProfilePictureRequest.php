<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfilePictureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // Max 5MB
        ];
    }
    
    public function messages(): array
    {
        return [
            'profile_picture.required' => 'ملف الصورة مطلوب.',
            'profile_picture.image' => 'الملف المرفوع يجب أن يكون صورة.',
            'profile_picture.mimes' => 'يُسمح فقط بملفات الصور من نوع: jpeg, png, jpg, gif.',
            'profile_picture.max' => 'حجم الصورة لا يجب أن يتجاوز 5 ميجابايت.',
        ];
    }
}