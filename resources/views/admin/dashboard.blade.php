{{-- Extend the admin layout file --}}
@extends('layouts.admin')

{{-- Define the header section --}}
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Admin Dashboard') }}
    </h2>
@endsection

{{-- Define the main content section --}}
@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Welcome to the Admin Panel!") }}

                    {{-- Display summary data passed from the controller --}}
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"> {{-- Adjusted grid for more columns --}}
                        {{-- Users Card --}}
                        <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Total Users') }}</h4>
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $userCount ?? 'N/A' }}</p>
                        </div>
                        {{-- Products Card --}}
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Total Products') }}</h4>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $productCount ?? 'N/A' }}</p>
                        </div>
                        {{-- Pending Orders Card --}}
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Pending Orders') }}</h4>
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingOrdersCount ?? 'N/A' }}</p>
                        </div>
                         {{-- Tourist Sites Card --}}
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Total Tourist Sites') }}</h4>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $touristSiteCount ?? 'N/A' }}</p>
                        </div>
                         {{-- Hotels Card --}}
                          <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Total Hotels') }}</h4>
                            <p class="text-2xl font-bold text-teal-600 dark:text-teal-400">{{ $hotelCount ?? 'N/A' }}</p>
                        </div>
                         {{-- Articles Card --}}
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Published Articles') }}</h4>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $articleCount ?? 'N/A' }}</p>
                        </div>
                        {{-- Hotel Bookings Card --}}
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Total Hotel Bookings') }}</h4>
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $hotelBookingCount ?? 'N/A' }}</p>
                        </div>
                        {{-- Product Categories Card --}}
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Product Categories') }}</h4>
                            <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $productCategoryCount ?? 'N/A' }}</p>
                        </div>
                        {{-- Site Categories Card --}}
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Site Categories') }}</h4>
                            <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $siteCategoryCount ?? 'N/A' }}</p>
                        </div>
                         {{-- Add more summary stats sections --}}
                    </div>


                    {{-- Links to managed sections --}}
                    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                         <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Manage Sections') }}</h3>
                         <ul class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"> {{-- Adjusted grid for more columns --}}
                             <!-- Management Links -->
                             <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('admin.users.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-indigo-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                         <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M10 20v-2a3 3 0 013-3h4a3 3 0 013 3v2M3 8l.877-.5m0 7.006l-.877-.5M19.123 8l.877-.5m-.877 7.006l.877-.5M9 10a3 3 0 11-6 0 3 3 0 016 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Users') }}</p>
                                         <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $userCount ?? '...' }} Total</p> {{-- Display count --}}
                                     </div>
                                 </a>
                             </li>
                             <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('admin.products.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-green-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                         <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7m0 4v4m0-8a2 2 0 012-2h2a2 2 0 012 2v4m-6 4a2 2 0 012-2h2a2 2 0 012 2v4m-6-4h.01M6 16h.01M6 12h.01M6 8h.01M4 20h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Products') }}</p>
                                         <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $productCount ?? '...' }} Total</p> {{-- Display count --}}
                                     </div>
                                 </a>
                             </li>
                              <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('admin.product-orders.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-pink-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Product Orders') }}</p>
                                          <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $pendingOrdersCount ?? '...' }} Pending</p> {{-- Display pending count --}}
                                     </div>
                                 </a>
                             </li>
                             <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('admin.product-categories.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-gray-600 text-white text-sm font-medium rounded-full h-10 w-10">
                                          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7l3-4m0 0l3 4m-3-4v16m2-16h-4m4 0h4"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Product Categories') }}</p>
                                          <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $productCategoryCount ?? '...' }} Total</p> {{-- Display count --}}
                                     </div>
                                 </a>
                             </li>
                             <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('admin.tourist-sites.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-yellow-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Tourist Sites') }}</p>
                                          <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $touristSiteCount ?? '...' }} Total</p> {{-- Display count --}}
                                     </div>
                                 </a>
                             </li>
                             <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('admin.site-categories.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-orange-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7l3-4m0 0l3 4m-3-4v16m2-16h-4m4 0h4"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Site Categories') }}</p>
                                          <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $siteCategoryCount ?? '...' }} Total</p> {{-- Display count --}}
                                     </div>
                                 </a>
                             </li>
                             <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('admin.hotels.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-teal-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2.5a2.5 2.5 0 002.5-2.5V9a2.5 2.5 0 00-2.5-2.5H19m0 14v-9m-14 9H3.5A2.5 2.5 0 011 18.5V10A2.5 2.5 0 013.5 7.5H5m0 14v-7.5"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Hotels') }}</p>
                                          <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $hotelCount ?? '...' }} Total</p> {{-- Display count --}}
                                     </div>
                                 </a>
                             </li>
                             <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('admin.hotel-bookings.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-cyan-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                         <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Hotel Bookings') }}</p>
                                          <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $hotelBookingCount ?? '...' }} Total</p> {{-- Display count --}}
                                     </div>
                                 </a>
                             </li>
                             <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('admin.articles.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-purple-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.582 5.477 3.5 6.253M12 6.253C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.418 1.253m-13.955 14L12 17.559m-2.054-2.282v-.635a4.25 4.25 0 014.25-4.25v-.635C14.582 9.477 16.082 10 17.5 10c1.418 0 2.918-.523 3.5-1.253v-.635m-18 7v-.635C5.418 9.477 6.918 10 8.5 10s3.082-.523 3.5-1.253v-.635"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Articles') }}</p>
                                          <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $articleCount ?? '...' }} Published</p> {{-- Display count --}}
                                     </div>
                                 </a>
                             </li>
                              <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('admin.site-experiences.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-rose-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                         <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.218A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.218A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Site Experiences') }}</p>
                                         {{-- Optional: Add count for experiences if fetched --}}
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