@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Manage Site Experiences') }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                     {{-- Info messages from redirects (e.g., "Admin cannot create...") --}}
                     @if (session('info'))
                        <div class="mb-4 font-medium text-sm text-blue-600 dark:text-blue-400">
                            {{ session('info') }}
                        </div>
                    @endif


                    {{-- No Create link as per typical admin moderation --}}
                    {{-- <div class="mb-4">... Add New Experience link (if admin creation is allowed) ...</div> --}}


                    {{-- Filter Options --}}
                     <div class="mb-4 border-b border-gray-200 dark:border-gray-700 pb-4">
                         <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-3">{{ __('Filter Experiences') }}</h3>
                         <form method="GET" action="{{ route('admin.site-experiences.index') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                             {{-- Site Filter --}}
                             <div>
                                 <x-input-label for="site_filter" :value="__('Tourist Site')" />
                                 <select id="site_filter" name="site_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                     <option value="">{{ __('All Sites') }}</option>
                                     @foreach ($sites as $site)
                                         <option value="{{ $site->id }}" {{ (string) request('site_id') === (string) $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                     @endforeach
                                 </select>
                             </div>
                             {{-- Add Date filters etc. here --}}

                             <div class="col-span-full flex items-center justify-end mt-3">
                                  <x-primary-button type="submit" class="mr-2">{{ __('Apply Filters') }}</x-primary-button>
                                   @if (request('user_id') || request('site_id')) {{-- Check if any filter is active --}}
                                        <a href="{{ route('admin.site-experiences.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">{{ __('Clear Filters') }}</a>
                                   @endif
                             </div>

                         </form>
                     </div>


                    {{-- Site Experiences Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('ID') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Photo') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Title') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('User') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Tourist Site') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Visit Date') }}
                                    </th>
                                     <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Created At') }}
                                    </th>
                                    {{-- Optional: Moderation Status Column --}}
                                    {{-- <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Moderation Status') }}
                                    </th> --}}
                                    <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($siteExperiences as $experience)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $experience->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                             @if ($experience->photo_url)
                                                <img src="{{ asset($experience->photo_url) }}" alt="{{ $experience->title ?? 'Experience Photo' }}" class="h-10 w-10 rounded object-cover">
                                            @else
                                                <span class="text-gray-400">{{ __('No Photo') }}</span>
                                            @endif
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $experience->title ?? Str::limit($experience->content, 50) }} {{-- Show title or excerpt of content --}}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $experience->user->username ?? 'N/A' }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $experience->site->name ?? 'N/A' }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $experience->visit_date ? $experience->visit_date->format('Y-m-d') : '-' }}
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $experience->created_at->format('Y-m-d H:i') }}
                                        </td>
                                        {{-- Optional: Moderation Status Cell --}}
                                        {{-- <td>{{ $experience->moderation_status }}</td> --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                            <a href="{{ route('admin.site-experiences.show', $experience) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-3">View</a>
                                            {{-- No Edit link as per controller logic --}}
                                            {{-- <a href="{{ route('admin.site-experiences.edit', $experience) }}" class="text-yellow-600 hover:text-yellow-900 ...">Edit</a> --}}
                                            <form action="{{ route('admin.site-experiences.destroy', $experience) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600" onclick="return confirm('Are you sure you want to delete this site experience? This action cannot be undone.');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                     {{-- Pagination Links --}}
                     <div class="mt-4">
                         {{ $siteExperiences->appends(request()->query())->links() }} {{-- Appends current query params for filters --}}
                     </div>

                </div>
            </div>
        </div>
    </div>
@endsection