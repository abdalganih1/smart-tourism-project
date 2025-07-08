@extends('layouts.admin') {{-- Reuse the admin layout --}}

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Hotel Manager Dashboard') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Welcome, Hotel Manager!") }}

                    {{-- Display summary data --}}
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Managed Hotels') }}</h4>
                            <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $hotelCount ?? 'N/A' }}</p>
                        </div>
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Total Rooms Managed') }}</h4>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ $totalRooms ?? 'N/A' }}</p>
                        </div>
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Upcoming Bookings') }}</h4>
                            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $upcomingBookingsCount ?? 'N/A' }}</p>
                        </div>
                        {{-- Add more summary stats --}}
                    </div>

                    {{-- Links to managed sections --}}
                    <div class="mt-6">
                         <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Manage Your Hotels & Bookings') }}</h3>
                         <ul class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                             <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('hotelmanager.hotels.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-indigo-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5m10 0h2m-2 0h-5m-9 0H3m2 0h5"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Your Hotels') }}</p>
                                         <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $hotelCount ?? '...' }} Managed</p>
                                     </div>
                                 </a>
                             </li>
                              <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('hotelmanager.hotel-bookings.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-teal-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Hotel Bookings') }}</p>
                                         <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $upcomingBookingsCount ?? '...' }} Upcoming</p>
                                     </div>
                                 </a>
                             </li>
                              {{-- Add link to manage rooms if needed, or access via hotel details --}}
                               <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('hotelmanager.hotel-rooms.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-purple-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2H7a2 2 0 00-2 2v2m7-4h.01M7 16h.01"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Rooms') }}</p>
                                          <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $totalRooms ?? '...' }} Total</p>
                                     </div>
                                 </a>
                             </li>
                         </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection