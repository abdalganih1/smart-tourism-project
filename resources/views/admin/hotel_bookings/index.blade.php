@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Manage Hotel Bookings') }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg|px-8">
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

                    {{-- No Create link as per requirements (only index/show) --}}

                     {{-- Filter Options --}}
                     <div class="mb-4 border-b border-gray-200 dark:border-gray-700 pb-4">
                         <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-3">{{ __('Filter Bookings') }}</h3>
                         <form method="GET" action="{{ route('admin.hotel-bookings.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                             {{-- Status Filter --}}
                             <div>
                                 <x-input-label for="status_filter" :value="__('Status')" />
                                 <select id="status_filter" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                     <option value="">{{ __('All Statuses') }}</option>
                                     @foreach ($statuses as $statusOption)
                                         <option value="{{ $statusOption }}" {{ request('status') === $statusOption ? 'selected' : '' }}>{{ __($statusOption) }}</option>
                                     @endforeach
                                 </select>
                             </div>
                              {{-- Hotel Filter --}}
                             <div>
                                 <x-input-label for="hotel_filter" :value="__('Hotel')" />
                                 <select id="hotel_filter" name="hotel_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                     <option value="">{{ __('All Hotels') }}</option>
                                     @foreach ($hotels as $hotel)
                                         <option value="{{ $hotel->id }}" {{ (string) request('hotel_id') === (string) $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                                     @endforeach
                                 </select>
                             </div>
                             {{-- User Filter --}}
                             <div>
                                 <x-input-label for="user_filter" :value="__('User')" />
                                 <select id="user_filter" name="user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                     <option value="">{{ __('All Users') }}</option>
                                     @foreach ($users as $user)
                                         <option value="{{ $user->id }}" {{ (string) request('user_id') === (string) $user->id ? 'selected' : '' }}>{{ $user->username }}</option>
                                     @endforeach
                                 </select>
                             </div>
                             {{-- Add Date filters etc. here --}}

                             <div class="col-span-full flex items-center justify-end mt-3">
                                  <x-primary-button type="submit" class="mr-2">{{ __('Apply Filters') }}</x-primary-button>
                                   @if (request('status') || request('hotel_id') || request('user_id')) {{-- Check if any filter is active --}}
                                        <a href="{{ route('admin.hotel-bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">{{ __('Clear Filters') }}</a>
                                   @endif
                             </div>

                         </form>
                     </div>


                    {{-- Hotel Bookings Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Booking ID') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('User') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Hotel') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Room / Type') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Dates') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Guests') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Total Amount') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Payment') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($hotelBookings as $booking)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $booking->id }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $booking->user->username ?? 'N/A' }} {{-- Display user's username --}}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $booking->room->hotel->name ?? 'N/A' }} {{-- Display hotel name --}}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $booking->room->room_number ?? 'N/A' }} / {{ $booking->room->type->name ?? 'N/A' }} {{-- Display room number and type --}}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $booking->check_in_date->format('Y-m-d') }} to {{ $booking->check_out_date->format('Y-m-d') }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $booking->num_adults }} Adult(s) {{ $booking->num_children > 0 ? ', ' . $booking->num_children . ' Child(ren)' : '' }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ number_format($booking->total_amount, 2) }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($booking->booking_status === 'Confirmed' || $booking->booking_status === 'Completed') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                                @elseif($booking->booking_status === 'PendingConfirmation') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                                @else bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                                @endif">
                                                {{ __($booking->booking_status) }}
                                            </span>
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($booking->payment_status === 'Paid') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                                @elseif($booking->payment_status === 'Unpaid') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100
                                                @endif">
                                                {{ __($booking->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                            <a href="{{ route('admin.hotel-bookings.show', $booking) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-3">View</a>
                                            {{-- Optional: Add Update Status buttons/forms here --}}
                                            {{-- Optional: Add Delete button (be careful with deleting bookings!) --}}
                                            {{-- <form action="{{ route('admin.hotel-bookings.destroy', $booking) }}" method="POST" class="inline"> ... </form> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                     {{-- Pagination Links --}}
                     <div class="mt-4">
                         {{ $hotelBookings->appends(request()->query())->links() }} {{-- Appends current query params for filters --}}
                     </div>

                </div>
            </div>
        </div>
    </div>
@endsection