@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Create Article') }}
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

                    {{-- Form for creating a new article --}}
                    <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data"> {{-- Added enctype --}}
                        @csrf

                        {{-- Article Information Section --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Article Details') }}</h3>
                            <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                {{-- Title --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="title" :value="__('Title')" />
                                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>

                                {{-- Excerpt --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="excerpt" :value="__('Excerpt (Short Summary - Optional)')" />
                                     <textarea id="excerpt" name="excerpt" rows="2" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('excerpt') }}</textarea>
                                    <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
                                </div>

                                {{-- Content --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="content" :value="__('Content')" />
                                     {{-- Consider using a rich text editor component here --}}
                                     <textarea id="content" name="content" rows="10" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('content') }}</textarea>
                                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                                </div>

                                {{-- Author --}}
                                <div class="sm:col-span-3">
                                     <x-input-label for="author_user_id" :value="__('Author')" />
                                    <select id="author_user_id" name="author_user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">{{ __('Select Author') }}</option>
                                         @foreach ($authors as $author)
                                            <option value="{{ $author->id }}" {{ old('author_user_id', Auth::id()) == $author->id ? 'selected' : '' }}> {{-- Default to current user --}}
                                                {{ $author->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('author_user_id')" class="mt-2" />
                                </div>

                                {{-- Status --}}
                                <div class="sm:col-span-3">
                                     <x-input-label for="status" :value="__('Status')" />
                                    <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        @foreach ($statuses as $status) {{-- Pass $statuses array from controller if needed --}}
                                            <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>

                                {{-- Published At --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="published_at" :value="__('Published At (Optional)')" />
                                    {{-- Use datetime-local type for date and time input --}}
                                    <x-text-input id="published_at" class="block mt-1 w-full" type="datetime-local" name="published_at" :value="old('published_at')" />
                                    <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                                </div>

                                {{-- Tags --}}
                                <div class="sm:col-span-3">
                                    <x-input-label for="tags" :value="__('Tags (Comma-separated - Optional)')" />
                                    <x-text-input id="tags" class="block mt-1 w-full" type="text" name="tags" :value="old('tags')" placeholder="e.g. سياحة,تاريخ,حلب" />
                                    <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                                </div>


                                {{-- Main Image Upload --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="main_image" :value="__('Main Image (Optional)')" />
                                    <input id="main_image" class="block mt-1 w-full text-gray-900 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" type="file" name="main_image" accept="image/*" />
                                    <x-input-error :messages="$errors->get('main_image')" class="mt-2" />
                                </div>

                                {{-- Video URL --}}
                                <div class="sm:col-span-full">
                                    <x-input-label for="video_url" :value="__('Video URL (Optional)')" />
                                    <x-text-input id="video_url" class="block mt-1 w-full" type="url" name="video_url" :value="old('video_url')" placeholder="e.g. https://www.youtube.com/watch?v=..." />
                                    <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
                                </div>


                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Create Article') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection