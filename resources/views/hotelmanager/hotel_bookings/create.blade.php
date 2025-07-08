@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Create New Hotel Booking') }}
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

                    <form action="{{ route('hotelmanager.hotel-bookings.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                             <div>
                                 <x-input-label for="hotel_id" :value="__('Hotel')" />
                                 {{-- Dropdown to select a managed hotel, then maybe filter rooms based on selection (requires JS or Livewire) --}}
                                 <select id="hotel_id" name="hotel_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                      <option value="">{{ __('Select Hotel') }}</option>
                                      @foreach ($managedHotels as $hotelOption)
                                          <option value="{{ $hotelOption->id }}" {{ old('hotel_id') == $hotelOption->id ? 'selected' : '' }}>{{ $hotelOption->name }}</option>
                                     @endforeach
                                 </select>
                                 <x-input-error class="mt-2" :messages="$errors->get('hotel_id')" />
                             </div>
                            {{-- Room Selection: Needs filtering based on selected hotel and possibly dates --}}
                            {{-- This is complex and might require Livewire or JS --}}
                            <div>
                                 <x-input-label for="room_id" :value="__('Room')" />
                                 {{-- A dropdown pre-filled with all rooms of managed hotels, or dynamically updated --}}
                                 <select id="room_id" name="room_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                      <option value="">{{ __('Select Room') }}</option>
                                      {{-- Loop through rooms of managed hotels --}}
                                       @foreach ($managedHotels as $hotelOption)
                                           @foreach ($hotelOption->rooms as $roomOption)
                                                <option value="{{ $roomOption->id }}" {{ old('room_id') == $roomOption->id ? 'selected' : '' }}>
                                                    {{ $hotelOption->name }} - #{{ $roomOption->room_number }} ({{ $roomOption->type->name ?? 'Room' }})
                                                </option>
                                           @endforeach
                                       @endforeach
                                 </select>
                                <x-input-error class="mt-2" :messages="$errors->get('room_id')" />
                            </div>


                             <div>
                                <x-input-label for="user_id" :value="__('Customer')" />
                                 {{-- Dropdown to select a user to book for --}}
                                 <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                      <option value="">{{ __('Select Customer') }}</option>
                                      @foreach ($users as $userOption)
                                          <option value="{{ $userOption->id }}" {{ old('user_id') == $userOption->id ? 'selected' : '' }}>{{ $userOption->username }} ({{ $userOption->email }})</option>
                                     @endforeach
                                 </select>
                                <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                             </div>

                              <div>
                                <x-input-label for="check_in_date" :value="__('Check-in Date')" />
                                <x-text-input id="check_in_date" name="check_in_date" type="date" class="mt-1 block w-full" :value="old('check_in_date')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('check_in_date')" />
                            </div>

                             <div>
                                <x-input-label for="check_out_date" :value="__('Check-out Date')" />
                                <x-text-input id="check_out_date" name="check_out_date" type="date" class="mt-1 block w-full" :value="old('check_out_date')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('check_out_date')" />
                            </div>

                             <div>
                                <x-input-label for="num_adults" :value="__('Number of Adults')" />
                                <x-text-input id="num_adults" name="num_adults" type="number" min="1" class="mt-1 block w-full" :value="old('num_adults', 1)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('num_adults')" />
                            </div>

                             <div>
                                <x-input-label for="num_children" :value="__('Number of Children (Optional)')" />
                                <x-text-input id="num_children" name="num_children" type="number" min="0" class="mt-1 block w-full" :value="old('num_children', 0)" />
                                <x-input-error class="mt-2" :messages="$errors->get('num_children')" />
                            </div>

                            {{-- Total Amount might be calculated on backend or client-side --}}
                            {{-- You might add an input here if manager manually sets price --}}
                            {{-- <div>
                                <x-input-label for="total_amount" :value="__('Total Amount')" />
                                <x-text-input id="total_amount" name="total_amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('total_amount')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('total_amount')" />
                            </div> --}}


                             <div class="md:col-span-2">
                                <x-input-label for="special_requests" :value="__('Special Requests (Optional)')" />
                                <textarea id="special_requests" name="special_requests" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="4">{{ old('special_requests') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('special_requests')" />
                            </div>


                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Create Booking') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection