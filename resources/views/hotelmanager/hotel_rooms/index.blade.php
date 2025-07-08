@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Managed Hotel Rooms') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Add 'Add New Room' button here if managers can add rooms --}}
                     {{--
                     <div class="flex justify-end mb-4">
                         <a href="{{ route('hotelmanager.hotel-rooms.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Add New Room') }}
                        </a>
                     </div>
                     --}}

                    @if (session('success'))
                         <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Hotel') }}</th> {{-- Assuming hotel relation loaded --}}
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Room Number') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Room Type') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Price Per Night') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Max Occupancy') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Available') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($rooms as $room)
                                    <tr>
                                         <td class="px-6 py-4 whitespace-nowrap">{{ $room->hotel->name ?? 'N/A' }}</td> {{-- Display hotel name --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $room->room_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $room->type->name ?? 'N/A' }}</td> {{-- Display room type name --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($room->price_per_night, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $room->max_occupancy }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap">
                                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $room->is_available_for_booking ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                {{ $room->is_available_for_booking ? __('Yes') : __('No') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                             <a href="{{ route('hotelmanager.hotel-rooms.show', $room) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 mr-3">{{ __('View Details') }}</a>
                                            <a href="{{ route('hotelmanager.hotel-rooms.edit', $room) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('Edit') }}</a>
                                            {{-- Delete is not in 'only' --}}
                                        </td>
                                    </tr>
                                @empty
                                     <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500 dark:text-gray-400">{{ __('No rooms found for your managed hotels.') }}</td>
                                    </tr>
                                @endforelse
                             </tbody>
                         </table>
                      </div>

                     <div class="mt-4">
                        {{ $rooms->links() }} {{-- Display pagination links --}}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection