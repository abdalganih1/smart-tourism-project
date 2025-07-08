@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Add New Activity') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg-px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600 dark:text-red-400">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.tourist-activities.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Activity Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                             <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="4">{{ old('description') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                             <div>
                                <x-input-label for="site_id" :value="__('Related Tourist Site (Optional)')" />
                                <select id="site_id" name="site_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                     <option value="">{{ __('Select Site') }}</option>
                                    @foreach ($sites as $site)
                                         <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('site_id')" />
                            </div>

                             <div>
                                <x-input-label for="location_text" :value="__('Alternative Location (Optional)')" />
                                <x-text-input id="location_text" name="location_text" type="text" class="mt-1 block w-full" :value="old('location_text')" />
                                <x-input-error class="mt-2" :messages="$errors->get('location_text')" />
                            </div>

                             <div>
                                <x-input-label for="start_datetime" :value="__('Start Date and Time')" />
                                {{-- You might need separate date and time inputs or a specific datetime picker --}}
                                {{-- Example using datetime-local input type if browser support is sufficient --}}
                                {{-- Note: Datetime-local format is YYYY-MM-DDTHH:mm --}}
                                <x-text-input id="start_datetime" name="start_datetime" type="datetime-local" class="mt-1 block w-full" :value="old('start_datetime')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('start_datetime')" />
                                {{-- If using separate date/time inputs, remember to combine them in prepareForValidation() --}}
                            </div>


                             <div>
                                <x-input-label for="duration_minutes" :value="__('Duration in Minutes (Optional)')" />
                                <x-text-input id="duration_minutes" name="duration_minutes" type="number" min="1" class="mt-1 block w-full" :value="old('duration_minutes')" />
                                <x-input-error class="mt-2" :messages="$errors->get('duration_minutes')" />
                            </div>

                             <div>
                                <x-input-label for="organizer_user_id" :value="__('Organizer (Optional)')" />
                                <select id="organizer_user_id" name="organizer_user_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                     <option value="">{{ __('Select Organizer') }}</option>
                                    @foreach ($organizers as $organizer)
                                         <option value="{{ $organizer->id }}" {{ old('organizer_user_id') == $organizer->id ? 'selected' : '' }}>{{ $organizer->username }} ({{ $organizer->user_type }})</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('organizer_user_id')" />
                            </div>

                             <div>
                                <x-input-label for="price" :value="__('Price (Optional)')" />
                                <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" :value="old('price', 0)" />
                                <x-input-error class="mt-2" :messages="$errors->get('price')" />
                            </div>

                             <div>
                                <x-input-label for="max_participants" :value="__('Max Participants (Optional)')" />
                                <x-text-input id="max_participants" name="max_participants" type="number" min="1" class="mt-1 block w-full" :value="old('max_participants')" />
                                <x-input-error class="mt-2" :messages="$errors->get('max_participants')" />
                            </div>

                            {{-- Add image/video file inputs if schema includes them --}}

                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Create Activity') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection