@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Edit Product') }}: {{ $product->name }}
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

                    <form action="{{ route('vendor.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- Use PUT method for updates --}}

                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Product Name')" />
                                {{-- Use $product->attribute or old('attribute', $product->attribute) for pre-filling --}}
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $product->name)" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                             <div>
                                <x-input-label for="price" :value="__('Price')" />
                                <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" :value="old('price', $product->price)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('price')" />
                            </div>

                             <div>
                                <x-input-label for="stock_quantity" :value="__('Stock Quantity')" />
                                <x-text-input id="stock_quantity" name="stock_quantity" type="number" class="mt-1 block w-full" :value="old('stock_quantity', $product->stock_quantity)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('stock_quantity')" />
                            </div>

                             <div>
                                <x-input-label for="category_id" :value="__('Category')" />
                                <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                     <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $category)
                                         <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                            </div>

                             <div>
                                <x-input-label for="color" :value="__('Color (Optional)')" />
                                <x-text-input id="color" name="color" type="text" class="mt-1 block w-full" :value="old('color', $product->color)" />
                                <x-input-error class="mt-2" :messages="$errors->get('color')" />
                            </div>

                             <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="4" required>{{ old('description', $product->description) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                             <div class="md:col-span-2">
                                 <x-input-label for="main_image" :value="__('Main Image (Optional)')" />
                                 @if ($product->main_image_url)
                                     <div class="mt-2">
                                         <img src="{{ asset($product->main_image_url) }}" alt="{{ $product->name }}" class="h-20 w-20 object-cover rounded-md">
                                         {{-- Optional: Add checkbox to remove image --}}
                                         {{-- <div class="mt-1">
                                             <input type="checkbox" name="remove_main_image" value="1" id="remove_main_image">
                                             <label for="remove_main_image" class="text-sm text-gray-600">Remove Image</label>
                                         </div> --}}
                                     </div>
                                 @endif
                                 <input id="main_image" name="main_image" type="file" class="mt-1 block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 dark:file:bg-indigo-500 dark:hover:file:bg-indigo-600">
                                <x-input-error class="mt-2" :messages="$errors->get('main_image')" />
                             </div>

                             <div class="md:col-span-2">
                                 <div class="flex items-center mt-4">
                                     <input id="is_available" name="is_available" type="checkbox" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                                    <label for="is_available" class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Available for Sale') }}</label>
                                 </div>
                                 <x-input-error class="mt-2" :messages="$errors->get('is_available')" />
                             </div>

                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Update Product') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection