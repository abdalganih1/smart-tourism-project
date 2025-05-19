@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('User Details') }}: {{ $user->username }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Action Buttons --}}
                    <div class="mb-4 flex justify-end">
                         <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Edit User') }}
                        </a>
                         <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                             @csrf
                             @method('DELETE')
                             <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this user?');">
                                 {{ __('Delete User') }}
                             </button>
                         </form>
                    </div>

                    {{-- User Account Details --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Account Details') }}</h3>
                        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('ID') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->id }}</dd>
                                </div>
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Username') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->username }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Email') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->email }}</dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('User Type') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->user_type }}</dd>
                                </div>
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Active') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                            {{ $user->is_active ? 'Yes' : 'No' }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                    <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Registered At') }}</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->created_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- User Profile Details (Check if profile exists) --}}
                    @if ($user->profile)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Profile Details') }}</h3>
                            <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Full Name') }}</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->profile->first_name }} {{ $user->profile->last_name }}</dd>
                                    </div>
                                     <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Father\'s Name') }}</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->profile->father_name ?? '-' }}</dd>
                                    </div>
                                     <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Mother\'s Name') }}</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->profile->mother_name ?? '-' }}</dd>
                                    </div>
                                     <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Bio') }}</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $user->profile->bio ?? '-' }}</dd>
                                    </div>
                                     {{-- Display profile picture/passport image if URLs exist --}}
                                      @if ($user->profile->profile_picture_url)
                                         <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                            <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Profile Picture') }}</dt>
                                            <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                                 <img src="{{ asset($user->profile->profile_picture_url) }}" alt="{{ __('Profile Picture') }}" class="h-20 w-20 rounded-full object-cover">
                                            </dd>
                                        </div>
                                      @endif
                                       @if ($user->profile->passport_image_url)
                                         <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                            <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Passport Image') }}</dt>
                                            <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                                 <a href="{{ asset($user->profile->passport_image_url) }}" target="_blank">View Passport Image</a>
                                            </dd>
                                        </div>
                                      @endif
                                </dl>
                            </div>
                        </div>
                    @else
                        <div class="mb-8 text-gray-600 dark:text-gray-400">{{ __('Profile details not available.') }}</div>
                    @endif

                    {{-- User Phone Numbers (Check if any exist) --}}
                    @if ($user->phoneNumbers->count() > 0)
                         <div class="mb-8">
                             <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Phone Numbers') }}</h3>
                             <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                 <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                     @foreach ($user->phoneNumbers as $phoneNumber)
                                         <li class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                             <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">
                                                 {{ $phoneNumber->phone_number }} {{ $phoneNumber->is_primary ? ' (' . __('Primary') . ')' : '' }}
                                             </dt>
                                              <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $phoneNumber->description ?? '-' }}</dd>
                                         </li>
                                     @endforeach
                                 </ul>
                             </div>
                         </div>
                    @else
                         <div class="mb-8 text-gray-600 dark:text-gray-400">{{ __('No phone numbers available.') }}</div>
                    @endif

                    {{-- Add sections for other related data (Products sold, Orders placed, etc.) if you want to display them here --}}
                    {{-- This would require loading those relationships in the controller's show method --}}


                    {{-- Back to Index Link --}}
                     <div class="mt-6">
                         <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                             {{ __('Back to Users List') }}
                         </a>
                     </div>

                </div>
            </div>
        </div>
    </div>
@endsection