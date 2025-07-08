@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Product Details') }}: {{ $product->name }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     <div class="flex justify-end mb-4">
                         <a href="{{ route('vendor.products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Edit Product') }}
                        </a>
                         <form action="{{ route('vendor.products.destroy', $product) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this product?');">
                                {{ __('Delete Product') }}
                            </button>
                        </form>
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if ($product->main_image_url)
                             <div class="md:col-span-2">
                                 <img src="{{ asset($product->main_image_url) }}" alt="{{ $product->name }}" class="w-full h-64 object-contain rounded-lg">
                             </div>
                        @endif

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Name') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</p>
                        </div>

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Price') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($product->price, 2) }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Stock Quantity') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $product->stock_quantity }}</p>
                        </div>

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Category') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $product->category->name ?? 'N/A' }}</p> {{-- Access category name --}}
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Color') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $product->color ?? __('N/A') }}</p>
                        </div>

                         <div>
                             <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Available for Sale') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $product->is_available ? __('Yes') : __('No') }}</p>
                         </div>

                         <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Description') }}</p>
                            <div class="mt-1 text-gray-900 dark:text-gray-100 prose dark:prose-invert max-w-none"> {{-- Use prose for basic markdown/html formatting --}}
                                {!! nl2br(e($product->description)) !!} {{-- nl2br to preserve line breaks, e() to escape HTML --}}
                            </div>
                        </div>

                         {{-- You can add sections here for ratings, comments related to the product if needed --}}

                    </div>

                     <div class="mt-6 flex justify-end">
                        <a href="{{ route('vendor.products.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('Back to Products List') }}</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection