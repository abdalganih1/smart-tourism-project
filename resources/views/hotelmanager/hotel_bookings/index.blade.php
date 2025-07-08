@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Managed Hotel Bookings') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Add 'Create New Booking' button if managers can create bookings --}}
                     <div class="flex justify-end mb-4">
                         <a href="{{ route('hotelmanager.hotel-bookings.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Create New Booking') }}
                        </a>
                     </div>


                    @if (session('success'))
                         <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Booking ID') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Hotel') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Room') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Customer') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Check-in') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Check-out') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Guests') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Amount') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($bookings as $booking)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->room->hotel->name ?? 'N/A' }}</td> {{-- Assuming room.hotel relation loaded --}}
                                        <td class="px-6 py-4 whitespace-nowrap">#{{ $booking->room->room_number ?? 'N/A' }} ({{ $booking->room->type->name ?? 'N/A' }})</td> {{-- Assuming room.type relation loaded --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->user->username ?? 'N/A' }}</td> {{-- Assuming user relation loaded --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->check_in_date->format('Y-m-d') }}</td> {{-- Format date --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->check_out_date->format('Y-m-d') }}</td> {{-- Format date --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->num_adults }} Adults, {{ $booking->num_children }} Kids</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($booking->total_amount, 2) }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap">
                                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100"> {{-- Customize status colors --}}
                                                {{ $booking->booking_status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                             <a href="{{ route('hotelmanager.hotel-bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 mr-3">{{ __('View Details') }}</a>
                                            <a href="{{ route('hotelmanager.hotel-bookings.edit', $booking) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-3">{{ __('Edit') }}</a>
                                             <form action="{{ route('hotelmanager.hotel-bookings.destroy', $booking) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600" onclick="return confirm('Are you sure you want to delete this booking?');">{{ __('Delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                     <tr>
                                        <td colspan="11" class="px-6 py-4 whitespace-nowrap text-center text-gray-500 dark:text-gray-400">{{ __('No bookings found for your managed hotels.') }}</td>
                                    </tr>
                                @endforelse
                             </tbody>
                         </table>
                      </div>

                     <div class="mt-4">
                        {{ $bookings->links() }} {{-- Display pagination links --}}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection