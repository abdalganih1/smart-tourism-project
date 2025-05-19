@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Create Hotel') }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Session Status/Messages --}}
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

                    {{-- Form for creating a new hotel --}}
                    <form method="POST" action="{{ route('admin.hotels.store') }}" enctype="multipart/form-data"> {{-- Added enctype --}}
                        @csrf

                        {{-- Hotel Information Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Hotel Details') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- Name --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="name" :value="__('Hotel Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                {{-- Star Rating --}}
                                 <div class="sm:col-span-2">
                                     <x-input-label for="star_rating" :value="__('Star Rating (1-7)')" />
                                     <x-text-input id="star_rating" class="block mt-1 w-full" type="number" name="star_rating" :value="old('star_rating')" min="1" max="7" />
                                     <x-input-error :messages="$errors->get('star_rating')" class="mt-2" />
                                 </div>

                                {{-- Managed By User --}}
                                <div class="sm:col-span-4">
                                     <x-input-label for="managed_by_user_id" :value="__('Managed By User (Optional)')" />
                                    <select id="managed_by_user_id" name="managed_by_user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">{{ __('Select User') }}</option>
                                         @foreach ($managers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('managed_by_user_id') == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('managed_by_user_id')" class="mt-2" />
                                </div>


                                 {{-- Description --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="description" :value="__('Description (Optional)')" />
                                     <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                {{-- Address Line 1 --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="address_line1" :value="__('Address Line 1 (Optional)')" />
                                    <x-text-input id="address_line1" class="block mt-1 w-full" type="text" name="address_line1" :value="old('address_line1')" />
                                    <x-input-error :messages="$errors->get('address_line1')" class="mt-2" />
                                </div>

                                 {{-- City --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="city" :value="__('City (Optional)')" />
                                    <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" />
                                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                </div>

                                 {{-- Country --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="country" :value="__('Country (Optional)')" />
                                    <x-text-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country', 'Syria')" />
                                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                                </div>

                                 {{-- Latitude --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="latitude" :value="__('Latitude (Optional)')" />
                                    <x-text-input id="latitude" class="block mt-1 w-full" type="number" step="0.00000001" name="latitude" :value="old('latitude')" />
                                    <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                                </div>

                                 {{-- Longitude --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="longitude" :value="__('Longitude (Optional)')" />
                                    <x-text-input id="longitude" class="block mt-1 w-full" type="number" step="0.00000001" name="longitude" :value="old('longitude')" />
                                    <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                                </div>

                                 {{-- Contact Phone --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="contact_phone" :value="__('Contact Phone (Optional)')" />
                                    <x-text-input id="contact_phone" class="block mt-1 w-full" type="text" name="contact_phone" :value="old('contact_phone')" />
                                    <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                                </div>

                                 {{-- Contact Email --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="contact_email" :value="__('Contact Email (Optional)')" />
                                    <x-text-input id="contact_email" class="block mt-1 w-full" type="email" name="contact_email" :value="old('contact_email')" />
                                    <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                                </div>


                                {{-- Main Image Upload --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="main_image" :value="__('Main Image (Optional)')" />
                                    <input id="main_image" class="block mt-1 w-full text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-indigo-600" type="file" name="main_image" accept="image/*" />
                                    <x-input-error :messages="$errors->get('main_image')" class="mt-2" />
                                </div>

                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Create Hotel') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection