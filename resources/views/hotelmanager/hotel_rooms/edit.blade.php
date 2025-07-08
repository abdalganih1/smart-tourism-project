@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Edit Room') }}: #{{ $hotelRoom->room_number }} ({{ $hotelRoom->hotel->name ?? 'N/A' }})
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

                     <form action="{{ route('hotelmanager.hotel-rooms.update', $hotelRoom) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Use PUT method for updates --}}

                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Hotel and Room Type might not be editable after creation --}}
                            {{--
                            <div>
                                 <x-input-label for="hotel_id" :value="__('Hotel')" />
                                 <select id="hotel_id" name="hotel_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                      @foreach ($managedHotels as $hotelOption)
                                          <option value="{{ $hotelOption->id }}" {{ old('hotel_id', $hotelRoom->hotel_id) == $hotelOption->id ? 'selected' : '' }}>{{ $hotelOption->name }}</option>
                                     @endforeach
                                 </select>
                                 <x-input-error class="mt-2" :messages="$errors->get('hotel_id')" />
                            </div>
                             <div>
                                <x-input-label for="room_type_id" :value="__('Room Type')" />
                                 <select id="room_type_id" name="room_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                      @foreach ($roomTypes as $roomType)
                                          <option value="{{ $roomType->id }}" {{ old('room_type_id', $hotelRoom->room_type_id) == $roomType->id ? 'selected' : '' }}>{{ $roomType->name }}</option>
                                     @endforeach
                                 </select>
                                <x-input-error class="mt-2" :messages="$errors->get('room_type_id')" />
                            </div>
                            --}}
                            {{-- Display Hotel and Type instead of editing --}}
                             <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Hotel') }}</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelRoom->hotel->name ?? 'N/A' }}</p>
                             </div>
                              <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Room Type') }}</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelRoom->type->name ?? 'N/A' }}</p>
                             </div>


                            <div>
                                <x-input-label for="room_number" :value="__('Room Number')" />
                                <x-text-input id="room_number" name="room_number" type="text" class="mt-1 block w-full" :value="old('room_number', $hotelRoom->room_number)" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('room_number')" />
                            </div>

                             <div>
                                <x-input-label for="price_per_night" :value="__('Price Per Night')" />
                                <x-text-input id="price_per_night" name="price_per_night" type="number" step="0.01" class="mt-1 block w-full" :value="old('price_per_night', $hotelRoom->price_per_night)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('price_per_night')" />
                            </div>

                             <div>
                                <x-input-label for="max_occupancy" :value="__('Max Occupancy')" />
                                <x-text-input id="max_occupancy" name="max_occupancy" type="number" min="1" class="mt-1 block w-full" :value="old('max_occupancy', $hotelRoom->max_occupancy)" />
                                <x-input-error class="mt-2" :messages="$errors->get('max_occupancy')" />
                            </div>

                             <div>
                                <x-input-label for="area_sqm" :value="__('Area (sqm, Optional)')" />
                                <x-text-input id="area_sqm" name="area_sqm" type="number" step="0.01" class="mt-1 block w-full" :value="old('area_sqm', $hotelRoom->area_sqm)" />
                                <x-input-error class="mt-2" :messages="$errors->get('area_sqm')" />
                            </div>


                             <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description (Optional)')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="4">{{ old('description', $hotelRoom->description) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>


                             <div class="md:col-span-2">
                                 <div class="flex items-center mt-4">
                                     <input id="is_available_for_booking" name="is_available_for_booking" type="checkbox" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('is_available_for_booking', $hotelRoom->is_available_for_booking) ? 'checked' : '' }}>
                                    <label for="is_available_for_booking" class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Available for Booking') }}</label>
                                 </div>
                                 <x-input-error class="mt-2" :messages="$errors->get('is_available_for_booking')" />
                             </div>

                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Update Room') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection