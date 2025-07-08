@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Edit Hotel Details') }}: {{ $hotel->name }}
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

                    <form action="{{ route('hotelmanager.hotels.update', $hotel) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- Use PUT method for updates --}}

                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Hotel Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $hotel->name)" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                             <div>
                                <x-input-label for="star_rating" :value="__('Star Rating')" />
                                <x-text-input id="star_rating" name="star_rating" type="number" min="1" max="7" class="mt-1 block w-full" :value="old('star_rating', $hotel->star_rating)" />
                                <x-input-error class="mt-2" :messages="$errors->get('star_rating')" />
                            </div>

                             <div>
                                <x-input-label for="city" :value="__('City')" />
                                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $hotel->city)" />
                                <x-input-error class="mt-2" :messages="$errors->get('city')" />
                            </div>

                            <div>
                                <x-input-label for="address_line1" :value="__('Address Line 1')" />
                                <x-text-input id="address_line1" name="address_line1" type="text" class="mt-1 block w-full" :value="old('address_line1', $hotel->address_line1)" />
                                <x-input-error class="mt-2" :messages="$errors->get('address_line1')" />
                            </div>

                             <div>
                                <x-input-label for="contact_phone" :value="__('Contact Phone')" />
                                <x-text-input id="contact_phone" name="contact_phone" type="text" class="mt-1 block w-full" :value="old('contact_phone', $hotel->contact_phone)" />
                                <x-input-error class="mt-2" :messages="$errors->get('contact_phone')" />
                            </div>

                             <div>
                                <x-input-label for="contact_email" :value="__('Contact Email')" />
                                <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 block w-full" :value="old('contact_email', $hotel->contact_email)" />
                                <x-input-error class="mt-2" :messages="$errors->get('contact_email')" />
                            </div>


                             <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="4">{{ old('description', $hotel->description) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                             <div class="md:col-span-2">
                                 <x-input-label for="main_image" :value="__('Main Image (Optional)')" />
                                 @if ($hotel->main_image_url)
                                     <div class="mt-2">
                                         <img src="{{ asset($hotel->main_image_url) }}" alt="{{ $hotel->name }}" class="h-20 w-20 object-cover rounded-md">
                                     </div>
                                 @endif
                                 <input id="main_image" name="main_image" type="file" class="mt-1 block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 dark:file:bg-indigo-500 dark:hover:file:bg-indigo-600">
                                <x-input-error class="mt-2" :messages="$errors->get('main_image')" />
                             </div>
                              {{-- Latitude/Longitude are often managed on a map, maybe simplify here --}}
                             <div>
                                <x-input-label for="latitude" :value="__('Latitude (Optional)')" />
                                <x-text-input id="latitude" name="latitude" type="text" class="mt-1 block w-full" :value="old('latitude', $hotel->latitude)" />
                                <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
                            </div>
                              <div>
                                <x-input-label for="longitude" :value="__('Longitude (Optional)')" />
                                <x-text-input id="longitude" name="longitude" type="text" class="mt-1 block w-full" :value="old('longitude', $hotel->longitude)" />
                                <x-input-error class="mt-2" :messages="$errors->get('longitude')" />
                            </div>

                            {{-- managed_by_user_id is not editable here --}}


                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Update Hotel') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection