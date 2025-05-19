@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Hotel Room Details') }}: {{ $hotelRoom->room_number }} ({{ $hotelRoom->hotel->name ?? '-' }})
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Action Buttons --}}
                    <div class="mb-4 flex justify-end">
                         <a href="{{ route('admin.hotel-rooms.edit', $hotelRoom) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Edit Room') }}
                        </a>
                         <form action="{{ route('admin.hotel-rooms.destroy', $hotelRoom) }}" method="POST" class="inline">
                             @csrf
                             @method('DELETE')
                             <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this room?');">
                                 {{ __('Delete Room') }}
                             </button>
                         </form>
                    </div>

                    {{-- Room Details --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room Details') }}</h3>
                        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('ID') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->id }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Hotel') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->hotel->name ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Room Number') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->room_number }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Type') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->type->name ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Price / Night') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ number_format($hotelRoom->price_per_night, 2) }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Area (sqm)') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->area_sqm ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Maximum Occupancy') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->max_occupancy ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Description') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->description ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Available for Booking') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $hotelRoom->is_available_for_booking ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                            {{ $hotelRoom->is_available_for_booking ? 'Yes' : 'No' }}
                                        </span>
                                    </dd>
                                </div>
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Created At') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->created_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Updated At') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotelRoom->updated_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- Associated Bookings (Check if any exist) --}}
                     @if ($hotelRoom->bookings->count() > 0)
                         <div class="mb-8">
                              <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Associated Bookings') }}</h3>
                              {{-- Optional: Link to manage bookings for this room --}}
                               {{-- <div class="mb-3">
                                   <a href="{{ route('admin.hotel-bookings.index', ['room_id' => $hotelRoom->id]) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('View/Manage Bookings') }}</a>
                               </div> --}}
                              <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
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
                                                      {{ __('Check-in') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Check-out') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Status') }}
                                                  </th>
                                              </tr>
                                          </thead>
                                          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                               @foreach ($hotelRoom->bookings as $booking)
                                                  <tr>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                          {{ $booking->id }}
                                                      </td>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                          {{ $booking->user->username ?? '-' }}
                                                      </td>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                           {{ $booking->check_in_date->format('Y-m-d') }}
                                                      </td>
                                                       <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                           {{ $booking->check_out_date->format('Y-m-d') }}
                                                      </td>
                                                       <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                          {{ $booking->booking_status }}
                                                      </td>
                                                  </tr>
                                              @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                              </div>
                         </div>
                     @else
                          <div class="mb-8 text-gray-600 dark:text-gray-400">{{ __('No bookings associated with this room.') }}</div>
                     @endif


                    {{-- Back to Index Link --}}
                     <div class="mt-6">
                         <a href="{{ route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                             {{ __('Back to Rooms List') }}
                         </a>
                     </div>

                </div>
            </div>
        </div>
    </div>
@endsection