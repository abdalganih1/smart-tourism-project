@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Site Experience Details') }}: #{{ $siteExperience->id }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Optional: Moderation Action Buttons (e.g., Approve/Reject) --}}
                    <div class="mb-4 flex justify-end">
                         {{-- Add buttons/forms here if you define custom routes/methods in controller --}}
                         {{-- <form action="{{ route('admin.site-experiences.moderate', $siteExperience) }}" method="POST" class="inline"> @csrf <input type="hidden" name="status" value="approved"> <button type="submit" class="bg-green-600 ...">Approve</button> </form> --}}
                         {{-- <form action="{{ route('admin.site-experiences.moderate', $siteExperience) }}" method="POST" class="inline"> @csrf <input type="hidden" name="status" value="rejected"> <button type="submit" class="bg-yellow-600 ...">Reject</button> </form> --}}

                         {{-- Delete Button --}}
                          <form action="{{ route('admin.site-experiences.destroy', $siteExperience) }}" method="POST" class="inline ml-2"> {{-- Add ml-2 for spacing if moderation buttons are added --}}
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this site experience? This action cannot be undone.');">
                                  {{ __('Delete Experience') }}
                              </button>
                          </form>
                    </div>

                    {{-- Experience Details --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Experience Details') }}</h3>
                         <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                             <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Experience ID') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $siteExperience->id }}</dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Created At') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $siteExperience->created_at->format('Y-m-d H:i:s') }}</dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Updated At') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $siteExperience->updated_at->format('Y-m-d H:i:s') }}</dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Visit Date') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $siteExperience->visit_date ? $siteExperience->visit_date->format('Y-m-d') : '-' }}</dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Title (Optional)') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $siteExperience->title ?? '-' }}</dd>
                                 </div>
                                  @if ($siteExperience->photo_url)
                                       <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                          <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Photo') }}</dt>
                                          <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                               <img src="{{ asset($siteExperience->photo_url) }}" alt="{{ $siteExperience->title ?? 'Experience Photo' }}" class="h-40 w-auto object-cover rounded">
                                          </dd>
                                      </div>
                                   @endif
                                   {{-- Optional: Moderation Status Field --}}
                                    {{--
                                    <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                       <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Moderation Status') }}</dt>
                                       <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $siteExperience->moderation_status ?? 'Pending' }}</dd>
                                   </div>
                                    --}}
                             </dl>
                         </div>
                    </div>

                     {{-- User Information --}}
                    @if ($siteExperience->user)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('User Information') }}</h3>
                             <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                 <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                      <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                          <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('User') }}</dt>
                                          <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                              {{ $siteExperience->user->username }}
                                              @if ($siteExperience->user->profile)
                                                  ({{ $siteExperience->user->profile->first_name }} {{ $siteExperience->user->profile->last_name }})
                                              @endif
                                          </dd>
                                      </div>
                                       <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                          <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Email') }}</dt>
                                          <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $siteExperience->user->email }}</dd>
                                      </div>
                                       {{-- Optional: Link to view/manage user --}}
                                        {{-- <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                           <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('User Profile') }}</dt>
                                           <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                               <a href="{{ route('admin.users.show', $siteExperience->user) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('View User Profile') }}</a>
                                           </dd>
                                       </div> --}}
                                 </dl>
                             </div>
                        </div>
                    @endif

                     {{-- Tourist Site Information --}}
                     @if ($siteExperience->site)
                         <div class="mb-8">
                              <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Tourist Site Information') }}</h3>
                               <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                   <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                       <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                           <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Site') }}</dt>
                                           <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                               {{ $siteExperience->site->name }}
                                                @if ($siteExperience->site->city)
                                                    ({{ $siteExperience->site->city }})
                                                @endif
                                            </dd>
                                       </div>
                                       {{-- Optional: Link to view site details --}}
                                        {{-- <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                           <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Site Details') }}</dt>
                                           <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                               <a href="{{ route('admin.tourist-sites.show', $siteExperience->site) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('View Site Details') }}</a>
                                           </dd>
                                       </div> --}}
                                   </dl>
                               </div>
                           </div>
                      @endif


                    {{-- Experience Content --}}
                    <div class="mb-8 mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Content') }}</h3>
                        <div class="mt-4 text-gray-700 dark:text-gray-300">
                             {{ $siteExperience->content }} {{-- Display plain text content --}}
                        </div>
                    </div>


                    {{-- Optional: Display associated Ratings or Comments (if implemented as related models) --}}
                    {{-- This would require loading those relationships --}}


                    {{-- Back to Index Link --}}
                     <div class="mt-6">
                         <a href="{{ route('admin.site-experiences.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                             {{ __('Back to Experiences List') }}
                         </a>
                     </div>

                </div>
            </div>
        </div>
    </div>
@endsection