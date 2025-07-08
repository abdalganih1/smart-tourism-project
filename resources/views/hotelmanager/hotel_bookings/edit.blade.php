@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Edit Hotel Booking') }} (ID: {{ $hotelBooking->id }})
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg-px-8">
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

                     <form action="{{ route('hotelmanager.hotel-bookings.update', $hotelBooking) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Use PUT method for updates --}}

                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                             {{-- Display read-only booking details that cannot be changed --}}
                             <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Hotel') }}</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->room->hotel->name ?? 'N/A' }}</p>
                             </div>
                              <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Room') }}</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">#{{ $hotelBooking->room->room_number ?? 'N/A' }} ({{ $hotelBooking->room->type->name ?? 'N/A' }})</p>
                             </div>
                              <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Customer') }}</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->user->username ?? 'N/A' }}</p>
                             </div>
                             <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Amount') }}</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($hotelBooking->total_amount, 2) }}</p>
                             </div>
                              <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Booked At') }}</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $hotelBooking->booked_at->format('Y-m-d H:i') }}</p>
                             </div>


                             {{-- Fields that the manager CAN edit (e.g., Status, maybe dates, guest counts) --}}
                              <div>
                                <x-input-label for="check_in_date" :value="__('Check-in Date')" />
                                <x-text-input id="check_in_date" name="check_in_date" type="date" class="mt-1 block w-full" :value="old('check_in_date', $hotelBooking->check_in_date->format('Y-m-d'))" required />
                                <x-input-error class="mt-2" :messages="$errors->get('check_in_date')" />
                            </div>

                             <div>
                                <x-input-label for="check_out_date" :value="__('Check-out Date')" />
                                <x-text-input id="check_out_date" name="check_out_date" type="date" class="mt-1 block w-full" :value="old('check_out_date', $hotelBooking->check_out_date->format('Y-m-d'))" required />
                                <x-input-error class="mt-2" :messages="$errors->get('check_out_date')" />
                            </div>

                              <div>
                                <x-input-label for="num_adults" :value="__('Number of Adults')" />
                                <x-text-input id="num_adults" name="num_adults" type="number" min="1" class="mt-1 block w-full" :value="old('num_adults', $hotelBooking->num_adults)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('num_adults')" />
                            </div>

                             <div>
                                <x-input-label for="num_children" :value="__('Number of Children (Optional)')" />
                                <x-text-input id="num_children" name="num_children" type="number" min="0" class="mt-1 block w-full" :value="old('num_children', $hotelBooking->num_children)" />
                                <x-input-error class="mt-2" :messages="$errors->get('num_children')" />
                            </div>


                             <div>
                                <x-input-label for="booking_status" :value="__('Booking Status')" />
                                <select id="booking_status" name="booking_status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                     {{-- Options based on Enum in DB schema --}}
                                     <option value="PendingConfirmation" {{ old('booking_status', $hotelBooking->booking_status) == 'PendingConfirmation' ? 'selected' : '' }}>{{ __('Pending Confirmation') }}</option>
                                     <option value="Confirmed" {{ old('booking_status', $hotelBooking->booking_status) == 'Confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                                     <option value="CancelledByUser" {{ old('booking_status', $hotelBooking->booking_status) == 'CancelledByUser' ? 'selected' : '' }}>{{ __('Cancelled by User') }}</option>
                                     <option value="CancelledByHotel" {{ old('booking_status', $hotelBooking->booking_status) == 'CancelledByHotel' ? 'selected' : '' }}>{{ __('Cancelled by Hotel') }}</option>
                                     <option value="Completed" {{ old('booking_status', $hotelBooking->booking_status) == 'Completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                     <option value="NoShow" {{ old('booking_status', $hotelBooking->booking_status) == 'NoShow' ? 'selected' : '' }}>{{ __('No Show') }}</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('booking_status')" />
                            </div>

                             <div>
                                <x-input-label for="payment_status" :value="__('Payment Status')" />
                                <select id="payment_status" name="payment_status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                     {{-- Options based on Enum in DB schema --}}
                                     <option value="Unpaid" {{ old('payment_status', $hotelBooking->payment_status) == 'Unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                                     <option value="Paid" {{ old('payment_status', $hotelBooking->payment_status) == 'Paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                     <option value="PaymentFailed" {{ old('payment_status', $hotelBooking->payment_status) == 'PaymentFailed' ? 'selected' : '' }}>{{ __('Payment Failed') }}</option>
                                     <option value="Refunded" {{ old('payment_status', $hotelBooking->payment_status) == 'Refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('payment_status')" />
                            </div>


                             <div class="md:col-span-2">
                                <x-input-label for="special_requests" :value="__('Special Requests (Optional)')" />
                                <textarea id="special_requests" name="special_requests" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="4">{{ old('special_requests', $hotelBooking->special_requests) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('special_requests')" />
                            </div>


                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Update Booking') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection