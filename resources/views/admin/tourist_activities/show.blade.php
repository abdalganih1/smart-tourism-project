@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Activity Details') }}: {{ $touristActivity->name }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg-px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     <div class="flex justify-end mb-4">
                         @can('update', $touristActivity) {{-- Check if user can edit --}}
                             <a href="{{ route('admin.tourist-activities.edit', $touristActivity) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                {{ __('Edit Activity') }}
                            </a>
                         @endcan
                         @can('delete', $touristActivity) {{-- Check if user can delete --}}
                         <form action="{{ route('admin.tourist-activities.destroy', $touristActivity) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this activity?');">
                                {{ __('Delete Activity') }}
                            </button>
                        </form>
                         @endcan
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Name') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $touristActivity->name }}</p>
                        </div>

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Related Tourist Site') }}</p>
                             <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $touristActivity->site->name ?? __('N/A') }}</p> {{-- Access site name --}}
                        </div>

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Location') }}</p>
                             <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $touristActivity->location_text ?? ($touristActivity->site ? $touristActivity->site->location_text : __('N/A')) }}</p>
                        </div>

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Start Date and Time') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $touristActivity->start_datetime->format('Y-m-d H:i') }}</p>
                        </div>

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Duration in Minutes') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $touristActivity->duration_minutes ?? __('N/A') }}</p>
                        </div>

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Organizer') }}</p>
                             <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $touristActivity->organizer->username ?? __('N/A') }}</p> {{-- Access organizer username --}}
                        </div>

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Price') }}</p>
                             <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($touristActivity->price, 2) ?? __('Free') }}</p>
                        </div>

                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Max Participants') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $touristActivity->max_participants ?? __('N/A') }}</p>
                        </div>

                         <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Description') }}</p>
                            <div class="mt-1 text-gray-900 dark:text-gray-100 prose dark:prose-invert max-w-none">
                                {!! nl2br(e($touristActivity->description)) !!}
                            </div>
                        </div>

                         {{-- Display image/video if applicable --}}

                    </div>

                     <div class="mt-6 flex justify-end">
                        <a href="{{ route('admin.tourist-activities.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('Back to Activities List') }}</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection