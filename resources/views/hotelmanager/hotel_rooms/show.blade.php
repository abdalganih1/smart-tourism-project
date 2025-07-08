@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Room Details') }}: #{{ $hotelRoom->room_number }} ({{ $hotelRoom->hotel->name ?? 'N/A' }})
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-end mb-4">
                         <a href="{{ route('hotelmanager.hotel-rooms.edit', $hotelRoom) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Edit Room') }}
                        </a>
                         {{-- Delete is not in 'only' --}}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Hotel') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelRoom->hotel->name ?? 'N/A' }}</p>
                         </div>
                          <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Room Type') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelRoom->type->name ?? 'N/A' }}</p>
                         </div>


                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Room Number') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelRoom->room_number }}</p>
                        </div>

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Price Per Night') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($hotelRoom->price_per_night, 2) }}</p>
                        </div>

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Max Occupancy') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelRoom->max_occupancy ?? 'N/A' }}</p>
                        </div>

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Area (sqm)') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelRoom->area_sqm ?? 'N/A' }}</p>
                        </div>

                         <div>
                             <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Available for Booking') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelRoom->is_available_for_booking ? __('Yes') : __('No') }}</p>
                         </div>

                         <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Description') }}</p>
                            <div class="mt-1 text-gray-900 dark:text-gray-100 prose dark:prose-invert max-w-none">
                                {!! nl2br(e($hotelRoom->description)) !!}
                            </div>
                        </div>

                         {{-- You can add sections here for upcoming bookings for this room --}}

                    </div>

                     <div class="mt-6 flex justify-end">
                        <a href="{{ route('hotelmanager.hotel-rooms.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('Back to Rooms List') }}</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection