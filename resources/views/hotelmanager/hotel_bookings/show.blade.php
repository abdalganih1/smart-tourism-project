@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Booking Details') }} (ID: {{ $hotelBooking->id }})
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg-px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-end mb-4">
                         <a href="{{ route('hotelmanager.hotel-bookings.edit', $hotelBooking) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Edit Booking') }}
                        </a>
                         <form action="{{ route('hotelmanager.hotel-bookings.destroy', $hotelBooking) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this booking?');">
                                {{ __('Delete Booking') }}
                            </button>
                        </form>
                    </div>


                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Booking ID') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->id }}</p>
                         </div>
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Customer') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->user->username ?? 'N/A' }} ({{ $hotelBooking->user->email ?? 'N/A' }})</p>
                         </div>
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Hotel') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->room->hotel->name ?? 'N/A' }}</p>
                         </div>
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Room') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">#{{ $hotelBooking->room->room_number ?? 'N/A' }} ({{ $hotelBooking->room->type->name ?? 'N/A' }})</p>
                         </div>
                          <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Check-in Date') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->check_in_date->format('Y-m-d') }}</p>
                         </div>
                          <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Check-out Date') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->check_out_date->format('Y-m-d') }}</p>
                         </div>
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Number of Adults') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->num_adults }}</p>
                         </div>
                          <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Number of Children') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->num_children }}</p>
                         </div>
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Amount') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($hotelBooking->total_amount, 2) }}</p>
                         </div>
                          <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Booking Status') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->booking_status }}</p>
                         </div>
                          <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Payment Status') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->payment_status }}</p>
                         </div>
                          <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Booked At') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->booked_at->format('Y-m-d H:i') }}</p>
                         </div>
                          @if ($hotelBooking->payment_transaction_id)
                              <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Payment Transaction ID') }}</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->payment_transaction_id }}</p>
                             </div>
                          @endif


                         <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Special Requests') }}</p>
                            <div class="mt-1 text-gray-900 dark:text-gray-100 prose dark:prose-invert max-w-none">
                                {!! nl2br(e($hotelBooking->special_requests)) !!}
                            </div>
                        </div>

                     </div>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('hotelmanager.hotel-bookings.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('Back to Bookings List') }}</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection