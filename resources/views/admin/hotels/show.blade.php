@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Hotel Details') }}: {{ $hotel->name }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Action Buttons --}}
                    <div class="mb-4 flex justify-end">
                         <a href="{{ route('admin.hotels.edit', $hotel) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Edit Hotel') }}
                        </a>
                         <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="POST" class="inline">
                             @csrf
                             @method('DELETE')
                             <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this hotel?');">
                                 {{ __('Delete Hotel') }}
                             </button>
                         </form>
                    </div>

                    {{-- Hotel Details --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Hotel Details') }}</h3>
                        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('ID') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->id }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Main Image') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                         @if ($hotel->main_image_url)
                                            <img src="{{ asset($hotel->main_image_url) }}" alt="{{ $hotel->name }}" class="h-40 w-40 object-cover rounded">
                                        @else
                                            <span class="text-gray-400">{{ __('No Image') }}</span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Name') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->name }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Star Rating') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->star_rating ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Description') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->description ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Address') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->address_line1 ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('City') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->city ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Country') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->country ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Coordinates') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                        @if($hotel->latitude && $hotel->longitude)
                                            Lat: {{ $hotel->latitude }}, Lng: {{ $hotel->longitude }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Contact Phone') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->contact_phone ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Contact Email') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->contact_email ?? '-' }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Managed By') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                        {{ $hotel->managedBy->username ?? '-' }}
                                        @if ($hotel->managedBy && $hotel->managedBy->profile)
                                            ({{ $hotel->managedBy->profile->first_name }} {{ $hotel->managedBy->profile->last_name }})
                                        @endif
                                    </dd>
                                </div>
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Created At') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->created_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Updated At') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $hotel->updated_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- Associated Rooms --}}
                     @if ($hotel->rooms->count() > 0)
                         <div class="mb-8">
                              <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Associated Rooms') }}</h3>
                               {{-- Optional: Link to manage rooms for this hotel --}}
                              {{-- <div class="mb-3">
                                  <a href="{{ route('admin.hotel-rooms.index', ['hotel_id' => $hotel->id]) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('View/Manage Rooms') }}</a>
                              </div> --}}
                              <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                  <div class="overflow-x-auto">
                                      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                          <thead class="bg-gray-50 dark:bg-gray-700">
                                              <tr>
                                                  <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Room Number') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Type') }}
                                                  </th>
                                                  <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Price / Night') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Available') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Max Occupancy') }}
                                                  </th>
                                                   <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                      {{ __('Bookings Count') }}
                                                  </th>
                                              </tr>
                                          </thead>
                                          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                               @foreach ($hotel->rooms as $room)
                                                  <tr>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                          {{ $room->room_number }}
                                                      </td>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                          {{ $room->type->name ?? '-' }}
                                                      </td>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                           {{ number_format($room->price_per_night, 2) }}
                                                      </td>
                                                       <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                           <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $room->is_available_for_booking ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                               {{ $room->is_available_for_booking ? 'Yes' : 'No' }}
                                                           </span>
                                                      </td>
                                                       <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                          {{ $room->max_occupancy ?? '-' }}
                                                      </td>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                         {{ $room->bookings->count() }} {{-- Display count of bookings --}}
                                                      </td>
                                                  </tr>
                                              @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                              </div>
                         </div>
                     @else
                          <div class="mb-8 text-gray-600 dark:text-gray-400">{{ __('No rooms associated with this hotel.') }}</div>
                     @endif


                    {{-- Back to Index Link --}}
                     <div class="mt-6">
                         <a href="{{ route('admin.hotels.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                             {{ __('Back to Hotels List') }}
                         </a>
                     </div>

                </div>
            </div>
        </div>
    </div>
@endsection