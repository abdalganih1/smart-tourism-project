@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Managed Hotels') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     @if (session('success'))
                         <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('City') }}</th>
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Star Rating') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Rooms Count') }}</th> {{-- Assuming rooms relation is eager loaded or accessible --}}
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($hotels as $hotel)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $hotel->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $hotel->city ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $hotel->star_rating ?? 'N/A' }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap">{{ $hotel->rooms->count() ?? 'N/A' }}</td> {{-- Assuming rooms relation is loaded --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                             <a href="{{ route('hotelmanager.hotels.show', $hotel) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 mr-3">{{ __('View') }}</a>
                                            <a href="{{ route('hotelmanager.hotels.edit', $hotel) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-3">{{ __('Edit Details') }}</a>
                                            {{-- Delete is not in 'only', so no delete form here --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500 dark:text-gray-400">{{ __('No hotels found managed by you.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $hotels->links() }} {{-- Display pagination links --}}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection