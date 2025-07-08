@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Hotel Details') }}: {{ $hotel->name }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     <div class="flex justify-end mb-4">
                         <a href="{{ route('hotelmanager.hotels.edit', $hotel) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Edit Hotel Details') }}
                        </a>
                        {{-- Delete is not in 'only' --}}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        @if ($hotel->main_image_url)
                             <div class="md:col-span-2">
                                 <img src="{{ asset($hotel->main_image_url) }}" alt="{{ $hotel->name }}" class="w-full h-64 object-contain rounded-lg">
                             </div>
                        @endif

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Hotel Name') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotel->name }}</p>
                        </div>
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Star Rating') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotel->star_rating ?? 'N/A' }}</p>
                        </div>
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('City') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotel->city ?? 'N/A' }}</p>
                         </div>
                          <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Address') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotel->address_line1 ?? 'N/A' }}</p>
                         </div>
                        <div>
                           <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Contact Phone') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotel->contact_phone ?? 'N/A' }}</p>
                        </div>
                         <div>
                           <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Contact Email') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotel->contact_email ?? 'N/A' }}</p>
                        </div>
                         <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Description') }}</p>
                            <div class="mt-1 text-gray-900 dark:text-gray-100 prose dark:prose-invert max-w-none">
                                {!! nl2br(e($hotel->description)) !!}
                            </div>
                        </div>
                         {{-- Location Coordinates --}}
                          @if ($hotel->latitude || $hotel->longitude)
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Location Coordinates') }}</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">Latitude: {{ $hotel->latitude ?? 'N/A' }}, Longitude: {{ $hotel->longitude ?? 'N/A' }}</p>
                                {{-- Add a map display here if desired --}}
                            </div>
                          @endif
                     </div>

                     <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">{{ __('Rooms in This Hotel') }}</h3>
                     {{-- Table of Rooms (Assuming rooms relation is loaded: $hotel->load('rooms.type')) --}}
                      <div class="overflow-x-auto">
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Room Number') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Room Type') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Price Per Night') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Max Occupancy') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Available') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($hotel->rooms as $room)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $room->room_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $room->type->name ?? 'N/A' }}</td> {{-- Assuming type relation is loaded --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($room->price_per_night, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $room->max_occupancy }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap">
                                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $room->is_available_for_booking ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                {{ $room->is_available_for_booking ? __('Yes') : __('No') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                             <a href="{{ route('hotelmanager.hotel-rooms.show', $room) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 mr-3">{{ __('View Details') }}</a>
                                            <a href="{{ route('hotelmanager.hotel-rooms.edit', $room) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('Edit') }}</a>
                                             {{-- Delete is not in 'only' --}}
                                        </td>
                                    </tr>
                                @empty
                                     <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500 dark:text-gray-400">{{ __('No rooms found for this hotel.') }}</td>
                                    </tr>
                                @endforelse
                             </tbody>
                         </table>
                      </div>

                     <div class="mt-6 flex justify-end">
                        <a href="{{ route('hotelmanager.hotels.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('Back to Hotels List') }}</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection