@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Manage Tourist Activities') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg-px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-end mb-4">
                         @can('create', App\Models\TouristActivity::class) {{-- Check if user can create --}}
                             <a href="{{ route('admin.tourist-activities.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Add New Activity') }}
                            </a>
                         @endcan
                    </div>


                    @if (session('success'))
                         <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Site') }}</th> {{-- Assuming site relation loaded --}}
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Location') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Start Time') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Price') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($activities as $activity)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $activity->name }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap">{{ $activity->site->name ?? __('N/A') }}</td> {{-- Display site name if linked --}}
                                         <td class="px-6 py-4 whitespace-nowrap">{{ $activity->location_text ?? ($activity->site ? $activity->site->location_text : __('N/A')) }}</td> {{-- Display location --}}
                                         <td class="px-6 py-4 whitespace-nowrap">{{ $activity->start_datetime->format('Y-m-d H:i') }}</td> {{-- Format date --}}
                                         <td class="px-6 py-4 whitespace-nowrap">{{ number_format($activity->price, 2) ?? __('Free') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @can('view', $activity) {{-- Check if user can view this specific activity --}}
                                             <a href="{{ route('admin.tourist-activities.show', $activity) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 mr-3">{{ __('View') }}</a>
                                            @endcan
                                            @can('update', $activity) {{-- Check if user can edit this specific activity --}}
                                            <a href="{{ route('admin.tourist-activities.edit', $activity) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600 mr-3">{{ __('Edit') }}</a>
                                             @endcan
                                             @can('delete', $activity) {{-- Check if user can delete this specific activity --}}
                                             <form action="{{ route('admin.tourist-activities.destroy', $activity) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600" onclick="return confirm('Are you sure you want to delete this activity?');">{{ __('Delete') }}</button>
                                            </form>
                                             @endcan
                                        </td>
                                    </tr>
                                @empty
                                     <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500 dark:text-gray-400">{{ __('No tourist activities found.') }}</td>
                                    </tr>
                                @endforelse
                             </tbody>
                         </table>
                      </div>

                     <div class="mt-4">
                        {{ $activities->links() }} {{-- Display pagination links --}}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection