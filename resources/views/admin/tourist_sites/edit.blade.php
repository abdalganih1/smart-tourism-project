@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Edit Tourist Site') }}: {{ $touristSite->name }}
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

                    {{-- Form for editing a tourist site --}}
                    <form method="POST" action="{{ route('admin.tourist-sites.update', $touristSite) }}" enctype="multipart/form-data"> {{-- Added enctype --}}
                        @csrf
                        @method('PUT') {{-- Use PUT method for update --}}

                        {{-- Site Information Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Site Details') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- Name --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="name" :value="__('Site Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $touristSite->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                {{-- Description --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="description" :value="__('Description')" />
                                     <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('description', $touristSite->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                {{-- Category --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="category_id" :value="__('Category')" />
                                    <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">{{ __('Select Category (Optional)') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $touristSite->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                {{-- Added By User --}}
                                <div class="sm:col-span-3">
                                     <x-input-label for="added_by_user_id" :value="__('Added By User (Optional)')" />
                                    <select id="added_by_user_id" name="added_by_user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">{{ __('Select User') }}</option>
                                         @foreach ($siteManagers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('added_by_user_id', $touristSite->added_by_user_id) == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('added_by_user_id')" class="mt-2" />
                                </div>


                                {{-- Location Text --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="location_text" :value="__('Location Text (Optional)')" />
                                    <x-text-input id="location_text" class="block mt-1 w-full" type="text" name="location_text" :value="old('location_text', $touristSite->location_text)" />
                                    <x-input-error :messages="$errors->get('location_text')" class="mt-2" />
                                </div>

                                 {{-- Latitude --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="latitude" :value="__('Latitude (Optional)')" />
                                    <x-text-input id="latitude" class="block mt-1 w-full" type="number" step="0.00000001" name="latitude" :value="old('latitude', $touristSite->latitude)" />
                                    <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                                </div>

                                 {{-- Longitude --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="longitude" :value="__('Longitude (Optional)')" />
                                    <x-text-input id="longitude" class="block mt-1 w-full" type="number" step="0.00000001" name="longitude" :value="old('longitude', $touristSite->longitude)" />
                                    <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                                </div>

                                 {{-- City --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="city" :value="__('City (Optional)')" />
                                    <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city', $touristSite->city)" />
                                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                </div>

                                 {{-- Country --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="country" :value="__('Country (Optional)')" />
                                    <x-text-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country', $touristSite->country)" />
                                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                                </div>

                                {{-- Main Image Upload --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="main_image" :value="__('Main Image (Optional)')" />
                                    @if ($touristSite->main_image_url)
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('Current Image:') }}</p>
                                            <img src="{{ asset($touristSite->main_image_url) }}" alt="{{ $touristSite->name }}" class="h-20 w-20 object-cover rounded">
                                        </div>
                                         <div class="mt-2">
                                             <label for="remove_main_image" class="inline-flex items-center">
                                                 <input id="remove_main_image" type="checkbox" name="remove_main_image" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-red-600 shadow-sm focus:ring-red-500 dark:focus:ring-red-600 dark:focus:ring-offset-gray-800" {{ old('remove_main_image') ? 'checked' : '' }}> {{-- Check old value --}}
                                                 <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remove current image') }}</span>
                                             </label>
                                         </div>
                                    @endif
                                    <input id="main_image" class="block mt-1 w-full text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" type="file" name="main_image" accept="image/*" />
                                    <x-input-error :messages="$errors->get('main_image')" class="mt-2" />
                                     @if ($errors->has('remove_main_image'))
                                        <x-input-error :messages="$errors->get('remove_main_image')" class="mt-2" />
                                    @endif
                                </div>

                                {{-- Video URL --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="video_url" :value="__('Video URL (Optional)')" />
                                    <x-text-input id="video_url" class="block mt-1 w-full" type="url" name="video_url" :value="old('video_url', $touristSite->video_url)" placeholder="e.g. https://www.youtube.com/watch?v=..." />
                                    <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
                                     {{-- Optional checkbox to remove video URL --}}
                                     {{--
                                     @if ($touristSite->video_url)
                                         <div class="mt-2">
                                             <label for="remove_video_url" class="inline-flex items-center">
                                                 <input id="remove_video_url" type="checkbox" name="remove_video" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-red-600 shadow-sm focus:ring-red-500 dark:focus:ring-red-600 dark:focus:ring-offset-gray-800" {{ old('remove_video') ? 'checked' : '' }}>
                                                 <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remove current video URL') }}</span>
                                             </label>
                                         </div>
                                     @endif
                                     @if ($errors->has('remove_video'))
                                        <x-input-error :messages="$errors->get('remove_video')" class="mt-2" />
                                    @endif
                                     --}}
                                </div>


                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Update Tourist Site') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection