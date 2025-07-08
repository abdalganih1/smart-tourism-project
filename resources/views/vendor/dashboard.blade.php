@extends('layouts.admin') {{-- Reuse the admin layout --}}

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Vendor Dashboard') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Welcome, Vendor!") }}

                    {{-- Display summary data --}}
                     <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Your Products') }}</h4>
                            <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ $productCount ?? 'N/A' }}</p>
                        </div>
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Total Orders') }}</h4>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ $totalOrdersWithVendorProductsCount ?? 'N/A' }}</p>
                        </div>
                         <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ __('Pending Orders') }}</h4>
                            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingOrdersWithVendorProductsCount ?? 'N/A' }}</p>
                        </div>
                         {{-- Add more summary stats --}}
                     </div>

                    {{-- Links to managed sections --}}
                     <div class="mt-6">
                         <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Manage Your Products & Orders') }}</h3>
                         <ul class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                              <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('vendor.products.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-green-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                         <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7m0 4v4m0-8a2 2 0 012-2h2a2 2 0 012 2v4m-6 4a2 2 0 012-2h2a2 2 0 012 2v4m-6-4h.01M6 16h.01M6 12h.01M6 8h.01M4 20h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Products') }}</p>
                                         <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $productCount ?? '...' }} Total</p>
                                     </div>
                                 </a>
                             </li>
                             <li class="col-span-1 flex shadow-sm rounded-md">
                                 <a href="{{ route('vendor.product-orders.index') }}" class="flex-1 flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                                     <div class="flex-shrink-0 flex items-center justify-center bg-pink-500 text-white text-sm font-medium rounded-full h-10 w-10">
                                          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01M6 12h.01M6 8h.01M4 20h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                     </div>
                                     <div class="flex-1 px-4 py-2 text-md">
                                         <p class="text-gray-900 dark:text-gray-100 font-medium">{{ __('Orders') }}</p>
                                          <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $totalOrdersWithVendorProductsCount ?? '...' }} Total</p>
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