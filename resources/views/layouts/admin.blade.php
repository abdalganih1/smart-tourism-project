<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Optional: Admin specific CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('css/admin.css') }}"> --}}

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Admin Navigation Bar -->
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                 <div class="flex justify-between h-16">
                     <div class="flex">
                         <!-- Logo -->
                         <div class="shrink-0 flex items-center">
                             <a href="{{ route('admin.dashboard') }}">
                                 <span class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ config('app.name', 'App') }} Admin</span>
                             </a>
                         </div>

                         <!-- Navigation Links (Admin Specific) -->
                         <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                             <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                 {{ __('Dashboard') }}
                             </x-nav-link>
                             <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                 {{ __('Users') }}
                             </x-nav-link>
                             <x-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                                 {{ __('Products') }}
                             </x-nav-link>
                              <x-nav-link :href="route('admin.tourist-sites.index')" :active="request()->routeIs('admin.tourist-sites.*')">
                                 {{ __('Sites') }}
                             </x-nav-link>
                             <x-nav-link :href="route('admin.hotels.index')" :active="request()->routeIs('admin.hotels.*')">
                                 {{ __('Hotels') }}
                             </x-nav-link>
                              <x-nav-link :href="route('admin.articles.index')" :active="request()->routeIs('admin.articles.*')">
                                 {{ __('Articles') }}
                             </x-nav-link>
                         </div>
                     </div>

                     <!-- Settings Dropdown (Admin User Menu) -->
                     <div class="hidden sm:flex sm:items-center sm:ms-6">
                          <x-dropdown align="right" width="48">
                              <x-slot name="trigger">
                                  <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                      <div>{{ Auth::user()->username }}</div>
                                      <div class="ms-1">
                                          <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                          </svg>
                                      </div>
                                  </button>
                              </x-slot>

                              <x-slot name="content">
                                  {{-- Link to Admin User Profile if applicable --}}
                                  {{-- <x-dropdown-link :href="route('admin.profile.edit')"> {{ __('Profile') }} </x-dropdown-link> --}}

                                  <!-- Authentication (Logout) -->
                                  {{-- تأكد من أن مسار 'logout' معرف في routes/auth.php --}}
                                  <form method="POST" action="{{ route('logout') }}">
                                      @csrf
                                      <x-dropdown-link :href="route('logout')"
                                              onclick="event.preventDefault();
                                                          this.closest('form').submit();">
                                          {{ __('Log Out') }}
                                      </x-dropdown-link>
                                  </form>
                              </x-slot>
                          </x-dropdown>
                      </div>

                     <!-- Hamburger (for responsive mobile view) -->
                    {{-- يمكنك إضافة جزء الـ Hamburger الخاص بـ Breeze هنا إذا أردت تصميم مستجيب --}}
                    {{--
                    <div class="-me-2 flex items-center sm:hidden">
                       <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                           <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                               <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                               <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                           </svg>
                       </button>
                   </div>
                   --}}
                 </div>
             </div>

             <!-- Responsive Navigation Menu -->
             {{-- يمكنك إضافة جزء الـ Responsive Menu الخاص بـ Breeze هنا إذا أردت تصميم مستجيب --}}
             {{--
             <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <!-- Add more responsive admin links -->
                </div>

                <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                    <div class="px-4">
                         <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->username }}</div>
                         <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                     <div class="mt-3 space-y-1">
                         <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                             {{ __('Log Out') }}
                         </x-responsive-nav-link>
                     </div>
                </div>
             </div>
             --}}

         </nav>


        <!-- Page Heading -->
        {{-- بدلاً من If isset($header)، نستخدم @yield('header') --}}
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                @yield('header') {{-- هنا سيتم عرض محتوى الـ section('header') من الـ View الفرعي --}}
            </div>
        </header>


        <!-- Page Content -->
        <main>
            @yield('content') {{-- هنا سيتم عرض محتوى الـ section('content') من الـ View الفرعي --}}
        </main>
    </div>

    @livewireScripts
</body>
</html>