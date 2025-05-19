@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Hotel Booking Details') }}: #{{ $hotelBooking->id }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Optional: Action Buttons (e.g., Update Status) --}}
                    <div class="mb-4 flex justify-end">
                         {{-- Add buttons for status updates if you define custom routes/methods in controller --}}
                         {{-- <button class="inline-flex items-center px-4 py-2 bg-indigo-600 ...">Mark as Processing</button> --}}
                         {{-- <button class="inline-flex items-center px-4 py-2 bg-green-600 ...">Mark as Shipped</button> --}}
                         {{-- <button class="inline-flex items-center px-4 py-2 bg-red-600 ...">Cancel Order</button> --}}
                         {{-- Optional: Add Delete button (be careful!) --}}
                          {{-- <form action="{{ route('admin.hotel-bookings.destroy', $hotelBooking) }}" method="POST" class="inline">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 ..." onclick="return confirm('Are you sure you want to delete this booking?');">{{ __('Delete Booking') }}</button>
                          </form> --}}
                    </div>

                    {{-- Booking Details --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Booking Details') }}</h3>
                         <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                             <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Booking ID') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelBooking->id }}</dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Booked At') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelBooking->booked_at->format('Y-m-d H:i:s') }}</dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Order Status') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                             @if($hotelBooking->booking_status === 'Confirmed' || $hotelBooking->booking_status === 'Completed') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                             @elseif($hotelBooking->booking_status === 'PendingConfirmation') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                             @else bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                             @endif">
                                             {{ __($hotelBooking->booking_status) }}
                                         </span>
                                     </dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Payment Status') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                             @if($hotelBooking->payment_status === 'Paid') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                             @elseif($hotelBooking->payment_status === 'Unpaid') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                             @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100
                                             @endif">
                                             {{ __($hotelBooking->payment_status) }}
                                         </span>
                                     </dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Total Amount') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ number_format($hotelBooking->total_amount, 2) }}</dd>
                                 </div>
                                  @if ($hotelBooking->payment_transaction_id)
                                      <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                         <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Transaction ID') }}</dt>
                                         <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelBooking->payment_transaction_id }}</dd>
                                     </div>
                                  @endif
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Check-in Date') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelBooking->check_in_date->format('Y-m-d') }}</dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Check-out Date') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelBooking->check_out_date->format('Y-m-d') }}</dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Number of Adults') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelBooking->num_adults }}</dd>
                                 </div>
                                   @if ($hotelBooking->num_children > 0)
                                      <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                         <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Number of Children') }}</dt>
                                         <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelBooking->num_children }}</dd>
                                     </div>
                                   @endif
                                    @if ($hotelBooking->special_requests)
                                       <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                          <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Special Requests') }}</dt>
                                          <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelBooking->special_requests }}</dd>
                                      </div>
                                   @endif
                             </dl>
                         </div>
                    </div>

                     {{-- Customer Information --}}
                    @if ($hotelBooking->user)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Customer Information') }}</h3>
                             <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                 <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                      <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                          <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('User') }}</dt>
                                          <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                              {{ $hotelBooking->user->username }}
                                              @if ($hotelBooking->user->profile)
                                                  ({{ $hotelBooking->user->profile->first_name }} {{ $hotelBooking->user->profile->last_name }})
                                              @endif
                                          </dd>
                                      </div>
                                       <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                          <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Email') }}</dt>
                                          <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelBooking->user->email }}</dd>
                                      </div>
                                       {{-- Optional: Display phone numbers if loaded --}}
                                        {{-- @if ($hotelBooking->user->phoneNumbers) ... @endif --}}
                                 </dl>
                             </div>
                        </div>
                    @endif

                     {{-- Room and Hotel Information --}}
                     @if ($hotelBooking->room)
                         <div class="mb-8">
                              <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room and Hotel Information') }}</h3>
                               <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                   <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                       <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                           <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Hotel') }}</dt>
                                           <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                               {{ $hotelBooking->room->hotel->name ?? '-' }}
                                                @if ($hotelBooking->room->hotel)
                                                    ({{ $hotelBooking->room->hotel->city ?? '-' }})
                                                @endif
                                            </dd>
                                       </div>
                                        <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                           <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room Number') }}</dt>
                                           <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelBooking->room->room_number ?? '-' }}</dd>
                                       </div>
                                       <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                           <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room Type') }}</dt>
                                           <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelBooking->room->type->name ?? '-' }}</dd>
                                       </div>
                                        <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                           <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room Price / Night') }}</dt>
                                           <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelBooking->room->price_per_night ? number_format($hotelBooking->room->price_per_night, 2) : '-' }}</dd>
                                       </div>
                                   </dl>
                               </div>
                           </div>
                      @endif


                    {{-- Back to Index Link --}}
                     <div class="mt-6">
                         <a href="{{ route('admin.hotel-bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                             {{ __('Back to Bookings List') }}
                         </a>
                     </div>

                </div>
            </div>
        </div>
    </div>
@endsection