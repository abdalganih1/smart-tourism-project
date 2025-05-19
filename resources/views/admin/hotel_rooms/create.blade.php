@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Create Hotel Room') }}
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

                    {{-- Form for creating a new room --}}
                    <form method="POST" action="{{ route('admin.hotel-rooms.store') }}">
                        @csrf

                        {{-- Room Information Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room Details') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- Hotel --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="hotel_id" :value="__('Hotel')" />
                                    <select id="hotel_id" name="hotel_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">{{ __('Select Hotel') }}</option>
                                         @foreach ($hotels as $hotel)
                                            {{-- Use preselectedHotelId from controller if present --}}
                                            <option value="{{ $hotel->id }}" {{ old('hotel_id', $preselectedHotelId) == $hotel->id ? 'selected' : '' }}>
                                                {{ $hotel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('hotel_id')" class="mt-2" />
                                </div>

                                {{-- Room Type --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="room_type_id" :value="__('Room Type')" />
                                    <select id="room_type_id" name="room_type_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">{{ __('Select Room Type') }}</option>
                                        @foreach ($roomTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('room_type_id')" class="mt-2" />
                                </div>

                                {{-- Room Number --}}
                                <div class="sm:col-span-2">
                                    <x-input-label for="room_number" :value="__('Room Number')" />
                                    <x-text-input id="room_number" class="block mt-1 w-full" type="text" name="room_number" :value="old('room_number')" required />
                                    <x-input-error :messages="$errors->get('room_number')" class="mt-2" />
                                </div>

                                 {{-- Price Per Night --}}
                                <div class="sm:col-span-2">
                                    <x-input-label for="price_per_night" :value="__('Price Per Night')" />
                                    <x-text-input id="price_per_night" class="block mt-1 w-full" type="number" step="0.01" name="price_per_night" :value="old('price_per_night')" required min="0" />
                                    <x-input-error :messages="$errors->get('price_per_night')" class="mt-2" />
                                </div>

                                {{-- Max Occupancy --}}
                                <div class="sm:col-span-2">
                                    <x-input-label for="max_occupancy" :value="__('Maximum Occupancy (Optional)')" />
                                    <x-text-input id="max_occupancy" class="block mt-1 w-full" type="number" name="max_occupancy" :value="old('max_occupancy', 1)" min="1" />
                                    <x-input-error :messages="$errors->get('max_occupancy')" class="mt-2" />
                                </div>

                                 {{-- Area (sqm) --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="area_sqm" :value="__('Area (sqm) (Optional)')" />
                                    <x-text-input id="area_sqm" class="block mt-1 w-full" type="number" step="0.01" name="area_sqm" :value="old('area_sqm')" min="0" />
                                    <x-input-error :messages="$errors->get('area_sqm')" class="mt-2" />
                                </div>

                                 {{-- Is Available For Booking --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="is_available_for_booking" :value="__('Available for Booking')" />
                                    <select id="is_available_for_booking" name="is_available_for_booking" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="1" {{ old('is_available_for_booking', true) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                        <option value="0" {{ old('is_available_for_booking', true) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('is_available_for_booking')" class="mt-2" />
                                </div>


                                 {{-- Description --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="description" :value="__('Description (Optional)')" />
                                     <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>


                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Create Room') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection