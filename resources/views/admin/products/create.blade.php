@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Create Product') }}
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

                    {{-- Form for creating a new product --}}
                    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data"> {{-- Added enctype for file uploads --}}
                        @csrf

                        {{-- Product Information Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Product Details') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- Name --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="name" :value="__('Product Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                {{-- Description --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="description" :value="__('Description')" />
                                     <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                {{-- Seller --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="seller_user_id" :value="__('Seller')" />
                                    <select id="seller_user_id" name="seller_user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">{{ __('Select Seller') }}</option>
                                         @foreach ($sellers as $seller)
                                            <option value="{{ $seller->id }}" {{ old('seller_user_id') == $seller->id ? 'selected' : '' }}>
                                                {{ $seller->username }} ({{ $seller->user_type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('seller_user_id')" class="mt-2" />
                                </div>

                                {{-- Category --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="category_id" :value="__('Category')" />
                                    <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">{{ __('Select Category') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                 {{-- Price --}}
                                <div class="sm:col-span-2">
                                    <x-input-label for="price" :value="__('Price')" />
                                    <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="old('price')" required min="0" />
                                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                </div>

                                {{-- Stock Quantity --}}
                                <div class="sm:col-span-2">
                                    <x-input-label for="stock_quantity" :value="__('Stock Quantity')" />
                                    <x-text-input id="stock_quantity" class="block mt-1 w-full" type="number" name="stock_quantity" :value="old('stock_quantity', 0)" required min="0" />
                                    <x-input-error :messages="$errors->get('stock_quantity')" class="mt-2" />
                                </div>

                                 {{-- Color --}}
                                 <div class="sm:col-span-2">
                                    <x-input-label for="color" :value="__('Color (Optional)')" />
                                    <x-text-input id="color" class="block mt-1 w-full" type="text" name="color" :value="old('color')" />
                                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                                </div>


                                 {{-- Is Available --}}
                                 <div class="sm:col-span-3">
                                    <x-input-label for="is_available" :value="__('Is Available')" />
                                    <select id="is_available" name="is_available" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="1" {{ old('is_available', true) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                        <option value="0" {{ old('is_available', true) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('is_available')" class="mt-2" />
                                </div>

                                {{-- Main Image Upload --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="main_image" :value="__('Main Image (Optional)')" />
                                    <input id="main_image" class="block mt-1 w-full text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" type="file" name="main_image" accept="image/*" />
                                    <x-input-error :messages="$errors->get('main_image')" class="mt-2" />
                                </div>

                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Create Product') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection