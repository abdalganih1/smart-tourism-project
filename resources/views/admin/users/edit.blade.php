@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Edit User') }}: {{ $user->username }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Session Status/Messages (Validation errors are handled by input-error component) --}}
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif
                     @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
                            {{ session('error') }}
                        </div>
                    @endif


                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT') {{-- Use PUT method for update --}}

                         {{-- User Account Information Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('User Account Information') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- Username --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="username" :value="__('Username')" />
                                    <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username', $user->username)" required autofocus />
                                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                                </div>

                                {{-- Email --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                {{-- Password (Optional - leave blank if not changing) --}}
                                 {{-- Note: Password confirmation is typically required if password is being changed --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="password" :value="__('Password (leave blank to keep current)')" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                {{-- Password Confirmation --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>

                                {{-- User Type --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="user_type" :value="__('User Type')" />
                                    <select id="user_type" name="user_type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        @foreach ($userTypes as $type)
                                            {{-- Use $user->user_type for the current user's type --}}
                                            <option value="{{ $type }}" {{ old('user_type', $user->user_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
                                </div>

                                {{-- Is Active --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="is_active" :value="__('Is Active')" />
                                    <select id="is_active" name="is_active" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        {{-- Use $user->is_active for the current user's status --}}
                                        <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                        <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                                </div>

                            </div>
                        </div>

                        {{-- User Profile Information Section (Check if profile exists or is being created) --}}
                         {{-- Assuming profile is always created with user, access $user->profile --}}
                         <div class="mb-6 mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('User Profile Information') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- First Name --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="first_name" :value="__('First Name')" />
                                    {{-- Use $user->profile->first_name --}}
                                    <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name', $user->profile->first_name ?? '')" required />
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                </div>

                                {{-- Last Name --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="last_name" :value="__('Last Name')" />
                                    {{-- Use $user->profile->last_name --}}
                                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name', $user->profile->last_name ?? '')" required />
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                </div>

                                 {{-- Father Name --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="father_name" :value="__('Father\'s Name')" />
                                     {{-- Use $user->profile->father_name --}}
                                    <x-text-input id="father_name" class="block mt-1 w-full" type="text" name="father_name" :value="old('father_name', $user->profile->father_name ?? '')" />
                                    <x-input-error :messages="$errors->get('father_name')" class="mt-2" />
                                </div>

                                 {{-- Mother Name --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="mother_name" :value="__('Mother\'s Name')" />
                                     {{-- Use $user->profile->mother_name --}}
                                    <x-text-input id="mother_name" class="block mt-1 w-full" type="text" name="mother_name" :value="old('mother_name', $user->profile->mother_name ?? '')" />
                                    <x-input-error :messages="$errors->get('mother_name')" class="mt-2" />
                                </div>

                                {{-- Bio --}}
                                <div class="sm:col-span-6">
                                    <x-input-label for="bio" :value="__('Bio')" />
                                    {{-- Use $user->profile->bio --}}
                                     <textarea id="bio" name="bio" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                                    <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                                </div>

                                {{-- File Uploads (Passport/Profile Picture) - Requires separate handling --}}
                                 {{-- Display current images if they exist --}}
                                  {{--
                                @if ($user->profile->passport_image_url)
                                    <div class="sm:col-span-6">
                                        <x-input-label :value="__('Current Passport Image')" />
                                        <p><a href="{{ asset($user->profile->passport_image_url) }}" target="_blank">View Current Image</a></p>
                                        <x-input-label for="passport_image" :value="__('Upload New Passport Image')" class="mt-2"/>
                                        <input id="passport_image" class="block mt-1 w-full" type="file" name="passport_image" />
                                        <x-input-error :messages="$errors->get('passport_image')" class="mt-2" />
                                    </div>
                                @endif
                                 @if ($user->profile->profile_picture_url)
                                    <div class="sm:col-span-6">
                                        <x-input-label :value="__('Current Profile Picture')" />
                                        <img src="{{ asset($user->profile->profile_picture_url) }}" alt="{{ __('Profile Picture') }}" class="h-20 w-20 rounded-full object-cover">
                                         <x-input-label for="profile_picture" :value="__('Upload New Profile Picture')" class="mt-2"/>
                                        <input id="profile_picture" class="block mt-1 w-full" type="file" name="profile_picture" />
                                        <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                                    </div>
                                @endif
                                --}}
                            </div>
                        </div>

                         {{-- Phone Numbers Section - More complex for edit form --}}
                        {{-- Needs logic to display existing phones, add new ones, mark primary, delete others --}}
                        {{-- Placeholder for primary phone number update --}}
                        {{--
                        <div class="mb-6 mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Phone Numbers') }}</h3>
                             <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                 @if ($user->phoneNumbers->count() > 0)
                                     @php $primaryPhone = $user->phoneNumbers->firstWhere('is_primary', true); @endphp
                                     <div class="sm:col-span-3">
                                         <x-input-label for="primary_phone_number" :value="__('Primary Phone Number')" />
                                         <x-text-input id="primary_phone_number" class="block mt-1 w-full" type="text" name="primary_phone_number" :value="old('primary_phone_number', $primaryPhone->phone_number ?? '')" />
                                         <x-input-error :messages="$errors->get('primary_phone_number')" class="mt-2" />
                                     </div>
                                     {{-- Display and allow editing/deleting other phones here --}}
                                 {{-- @else
                                      <div class="sm:col-span-3">
                                         <x-input-label for="primary_phone_number" :value="__('Primary Phone Number')" />
                                         <x-text-input id="primary_phone_number" class="block mt-1 w-full" type="text" name="primary_phone_number" :value="old('primary_phone_number')" />
                                         <x-input-error :messages="$errors->get('primary_phone_number')" class="mt-2" />
                                     </div>
                                 @endif
                                {{-- Add button to add new phone number fields dynamically --}}
                             {{-- </div>
                        </div>
                        --}}


                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Update User') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection