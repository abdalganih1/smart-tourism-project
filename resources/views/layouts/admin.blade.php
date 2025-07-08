<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Optional: Admin specific CSS if needed -->
    {{-- <link rel="stylesheet" href="{{ asset('css/admin.css') }}"> --}}

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Navigation Bar -->
        <nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700"> {{-- Added x-data="{ open: false }" here --}}
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                 <div class="flex justify-between h-16">
                     <div class="flex">
                         <!-- Logo -->
                         <div class="shrink-0 flex items-center">
                            @auth
                                {{-- Calculate the correct dashboard route for the logo based on user role --}}
                                @php
                                    $dashboardRoute = Auth::user()->isAdmin() ? route('admin.dashboard') : (
                                        Auth::user()->isHotelBookingManager() ? route('hotelmanager.dashboard') : (
                                        Auth::user()->isVendor() ? route('vendor.dashboard') : route('dashboard') // Default dashboard for others
                                        ));
                                @endphp
                                <a href="{{ $dashboardRoute }}">
                                     {{-- <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" /> --}}
                                     <span class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ config('app.name', 'App') }}</span> {{-- App Name Logo --}}
                                 </a>
                            @else
                                {{-- Logo link for logged-out users (e.g., to welcome page or login) --}}
                                <a href="{{ route('login') }}">
                                     <span class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ config('app.name', 'App') }}</span>
                                </a>
                            @endauth
                         </div>

                         <!-- Navigation Links (Desktop) -->
                         <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            @auth
                                {{-- Dashboard Link (Common but links/active state depend on role) --}}
                                @php
                                    // Calculate the active state for the dashboard link
                                    $isActiveDashboard = Auth::user()->isAdmin() ? request()->routeIs('admin.dashboard') : (
                                        Auth::user()->isHotelBookingManager() ? request()->routeIs('hotelmanager.dashboard') : (
                                        Auth::user()->isVendor() ? request()->routeIs('vendor.dashboard') : request()->routeIs('dashboard')
                                        ));
                                @endphp
                                {{-- Use the variables in the dynamic attributes --}}
                                <x-nav-link :href="$dashboardRoute" :active="$isActiveDashboard">
                                    {{ __('Dashboard') }}
                                </x-nav-link>

                                {{-- Conditional Links based on User Role --}}
                                @if (Auth::user()->isAdmin())
                                    {{-- Admin Links --}}
                                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')"> {{ __('Users') }} </x-nav-link>
                                    <x-nav-link :href="route('admin.product-categories.index')" :active="request()->routeIs('admin.product-categories.*')"> {{ __('Product Categories') }} </x-nav-link>
                                    <x-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')"> {{ __('Products') }} </x-nav-link>
                                    <x-nav-link :href="route('admin.product-orders.index')" :active="request()->routeIs('admin.product-orders.*')"> {{ __('Product Orders') }} </x-nav-link>

                                    <x-nav-link :href="route('admin.site-categories.index')" :active="request()->routeIs('admin.site-categories.*')"> {{ __('Site Categories') }} </x-nav-link>
                                    <x-nav-link :href="route('admin.tourist-sites.index')" :active="request()->routeIs('admin.tourist-sites.*')"> {{ __('Tourist Sites') }} </x-nav-link>
                                    <x-nav-link :href="route('admin.tourist-activities.index')" :active="request()->routeIs('admin.tourist-activities.*')"> {{ __('Activities') }} </x-nav-link>

                                    <x-nav-link :href="route('admin.hotels.index')" :active="request()->routeIs('admin.hotels.*')"> {{ __('Hotels') }} </x-nav-link>
                                    <x-nav-link :href="route('admin.hotel-room-types.index')" :active="request()->routeIs('admin.hotel-room-types.*')"> {{ __('Room Types') }} </x-nav-link>
                                    <x-nav-link :href="route('admin.hotel-rooms.index')" :active="request()->routeIs('admin.hotel-rooms.*')"> {{ __('Rooms') }} </x-nav-link>
                                    <x-nav-link :href="route('admin.hotel-bookings.index')" :active="request()->routeIs('admin.hotel-bookings.*')"> {{ __('Hotel Bookings') }} </x-nav-link>

                                    <x-nav-link :href="route('admin.site-experiences.index')" :active="request()->routeIs('admin.site-experiences.*')"> {{ __('Experiences') }} </x-nav-link>
                                    <x-nav-link :href="route('admin.articles.index')" :active="request()->routeIs('admin.articles.*')"> {{ __('Articles') }} </x-nav-link>

                                    {{-- Add links for Ratings, Comments Moderation etc. if separate views exist --}}
                                    {{-- @can('viewAny', App\Models\Rating::class) --}} {{-- Example policy check --}}
                                    {{-- <x-nav-link :href="route('admin.ratings.index')" :active="request()->routeIs('admin.ratings.*')"> {{ __('Ratings') }} </x-nav-link> --}}
                                    {{-- @endcan --}}
                                    {{-- @can('viewAny', App\Models\Comment::class) --}} {{-- Example policy check --}}
                                    {{-- <x-nav-link :href="route('admin.comments.index')" :active="request()->routeIs('admin.comments.*')"> {{ __('Comments') }} </x-nav-link> --}}
                                    {{-- @endcan --}}


                                @elseif (Auth::user()->isHotelBookingManager())
                                    {{-- Hotel Manager Links --}}
                                    <x-nav-link :href="route('hotelmanager.hotels.index')" :active="request()->routeIs('hotelmanager.hotels.*')"> {{ __('Your Hotels') }} </x-nav-link>
                                    <x-nav-link :href="route('hotelmanager.hotel-rooms.index')" :active="request()->routeIs('hotelmanager.hotel-rooms.*')"> {{ __('Your Rooms') }} </x-nav-link>
                                    <x-nav-link :href="route('hotelmanager.hotel-bookings.index')" :active="request()->routeIs('hotelmanager.hotel-bookings.*')"> {{ __('Hotel Bookings') }} </x-nav-link>
                                    {{-- Add links for Hotel Room Types managed by this manager if applicable --}}
                                    {{-- <x-nav-link :href="route('hotelmanager.hotel-room-types.index')" :active="request()->routeIs('hotelmanager.hotel-room-types.*')"> {{ __('Room Types') }} </x-nav-link> --}}

                                @elseif (Auth::user()->isVendor())
                                    {{-- Vendor Links --}}
                                    <x-nav-link :href="route('vendor.products.index')" :active="request()->routeIs('vendor.products.*')"> {{ __('Your Products') }} </x-nav-link>
                                    <x-nav-link :href="route('vendor.product-orders.index')" :active="request()->routeIs('vendor.product-orders.*')"> {{ __('Your Orders') }} </x-nav-link>
                                    {{-- Add links for Product Categories if vendors can manage them --}}
                                    {{-- <x-nav-link :href="route('vendor.product-categories.index')" :active="request()->routeIs('vendor.product-categories.*')"> {{ __('Product Categories') }} </x-nav-link> --}}

                                @else
                                    {{-- Links for other user types if they use this layout --}}
                                     @if (Auth::user()->isEmployee())
                                         {{-- Employee Links (Example, adjust routes/names) --}}
                                          <x-nav-link :href="route('employee.dashboard')" :active="request()->routeIs('employee.dashboard')"> {{ __('Employee Dashboard') }} </x-nav-link>
                                         {{-- Add links relevant to employee tasks --}}
                                    @elseif (Auth::user()->isArticleWriter())
                                        {{-- Article Writer Links (Example, adjust routes/names) --}}
                                         <x-nav-link :href="route('writer.dashboard')" :active="request()->routeIs('writer.dashboard')"> {{ __('Writer Dashboard') }} </x-nav-link>
                                          <x-nav-link :href="route('writer.articles.index')" :active="request()->routeIs('writer.articles.*')"> {{ __('Your Articles') }} </x-nav-link>
                                    @else
                                         {{-- Fallback for unexpected roles using this layout --}}
                                         <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"> {{ __('User Dashboard') }} </x-nav-link>
                                    @endif

                                @endif
                            @endauth {{-- End @auth --}}
                         </div>
                     </div>

                     <!-- Settings Dropdown (User Menu - shows username, email, logout) -->
                     <div class="hidden sm:flex sm:items-center sm:ms-6">

                         <!-- Language Switcher -->
                         {{-- <div class="ms-3 relative">
                             <x-dropdown align="right" width="48">
                                 <x-slot name="trigger">
                                     <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                         <div>{{ strtoupper(App::getLocale()) }}</div> 
                                         <div class="ms-1">
                                             <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                 <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                             </svg>
                                         </div>
                                     </button>
                                 </x-slot>

                                 <x-slot name="content">
                                     <x-dropdown-link :href="route('set-locale', 'ar')">
                                         {{ __('العربية') }}
                                     </x-dropdown-link>
                                     <x-dropdown-link :href="route('set-locale', 'en')">
                                         {{ __('English') }}
                                     </x-dropdown-link>
                                 </x-slot>
                             </x-dropdown>
                         </div> --}}

                         {{-- The rest of the user settings dropdown --}}
                          <x-dropdown align="right" width="48">
                              <x-slot name="trigger">
                                   {{-- ... user button ... --}}
                                   <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                       @auth
                                           <div>{{ Auth::user()->username }}</div>
                                       @endauth
                                       <div class="ms-1">
                                           <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                               <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                           </svg>
                                       </div>
                                   </button>
                              </x-slot>

                              <x-slot name="content">
                                  @auth {{-- Only show these if logged in --}}
                                      {{-- Link to Role-specific Profile or general profile --}}
                                      <x-dropdown-link :href="route('profile')"> {{ __('Profile') }} </x-dropdown-link>

                                      <!-- Authentication (Logout) -->
                                      <form method="POST" action="{{ route('logout') }}">
                                          @csrf
                                          <x-dropdown-link :href="route('logout')"
                                                  onclick="event.preventDefault();
                                                              this.closest('form').submit();">
                                              {{ __('Log Out') }}
                                          </x-dropdown-link>
                                      </form>
                                  @else {{-- Show login/register if not logged in --}}
                                       <x-dropdown-link :href="route('login')"> {{ __('Log In') }} </x-dropdown-link>
                                       <x-dropdown-link :href="route('register')"> {{ __('Register') }} </x-dropdown-link>
                                  @endauth
                              </x-slot>
                          </x-dropdown>
                      </div>

                     <!-- Hamburger (for responsive mobile view) -->
                     {{-- Include the responsive navigation toggle from Breeze's app-layout --}}
                     {{-- The x-data="{ open: false }" should ideally be on the outer nav element --}}
                    <div class="-me-2 flex items-center sm:hidden">
                       <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                           <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                               <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                               <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                           </svg>
                       </button>
                   </div>
                 </div>
             </div>

             <!-- Responsive Navigation Menu -->
             {{-- Include the responsive menu content from Breeze's app-layout, applying similar role checks --}}
             {{-- The x-data="{ open: false }" should ideally be on the outer nav element --}}
             <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    {{-- Responsive Dashboard Link --}}
                    @auth {{-- Ensure user is logged in before calculating routes --}}
                         @php
                            // Re-calculate the correct dashboard route for responsive menu
                            $responsiveDashboardRoute = Auth::user()->isAdmin() ? route('admin.dashboard') : (
                                Auth::user()->isHotelBookingManager() ? route('hotelmanager.dashboard') : (
                                Auth::user()->isVendor() ? route('vendor.dashboard') : route('dashboard')
                                ));
                            // Re-calculate the active state for responsive dashboard link
                            $isResponsiveActiveDashboard = Auth::user()->isAdmin() ? request()->routeIs('admin.dashboard') : (
                                Auth::user()->isHotelBookingManager() ? request()->routeIs('hotelmanager.dashboard') : (
                                Auth::user()->isVendor() ? request()->routeIs('vendor.dashboard') : request()->routeIs('dashboard')
                                ));
                         @endphp
                         <x-responsive-nav-link :href="$responsiveDashboardRoute" :active="$isResponsiveActiveDashboard">
                             {{ __('Dashboard') }}
                         </x-responsive-nav-link>

                         {{-- Conditional Responsive Links based on User Role --}}
                         @if (Auth::user()->isAdmin())
                             {{-- Admin Responsive Links --}}
                             <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')"> {{ __('Users') }} </x-responsive-nav-link>
                             <x-responsive-nav-link :href="route('admin.product-categories.index')" :active="request()->routeIs('admin.product-categories.*')"> {{ __('Product Categories') }} </x-responsive-nav-link>
                             <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')"> {{ __('Products') }} </x-responsive-nav-link>
                             <x-responsive-nav-link :href="route('admin.product-orders.index')" :active="request()->routeIs('admin.product-orders.*')"> {{ __('Product Orders') }} </x-responsive-nav-link>
                             <x-responsive-nav-link :href="route('admin.site-categories.index')" :active="request()->routeIs('admin.site-categories.*')"> {{ __('Site Categories') }} </x-responsive-nav-link>
                             <x-responsive-nav-link :href="route('admin.tourist-sites.index')" :active="request()->routeIs('admin.tourist-sites.*')"> {{ __('Tourist Sites') }} </x-responsive-nav-link>
                             <x-responsive-nav-link :href="route('admin.tourist-activities.index')" :active="request()->routeIs('admin.tourist-activities.*')"> {{ __('Activities') }} </x-responsive-nav-link>
                             <x-responsive-nav-link :href="route('admin.hotels.index')" :active="request()->routeIs('admin.hotels.*')"> {{ __('Hotels') }} </x-responsive-nav-link>
                             <x-responsive-nav-link :href="route('admin.hotel-room-types.index')" :active="request()->routeIs('admin.hotel-room-types.*')"> {{ __('Room Types') }} </x-responsive-nav-link>
                             <x-responsive-nav-link :href="route('admin.hotel-rooms.index')" :active="request()->routeIs('admin.hotel-rooms.*')"> {{ __('Rooms') }} </x-responsive-nav-link>
                             <x-responsive-nav-link :href="route('admin.hotel-bookings.index')" :active="request()->routeIs('admin.hotel-bookings.*')"> {{ __('Hotel Bookings') }} </x-responsive-nav-link>
                              <x-responsive-nav-link :href="route('admin.site-experiences.index')" :active="request()->routeIs('admin.site-experiences.*')"> {{ __('Experiences') }} </x-responsive-nav-link>
                              <x-responsive-nav-link :href="route('admin.articles.index')" :active="request()->routeIs('admin.articles.*')"> {{ __('Articles') }} </x-responsive-nav-link>

                         @elseif (Auth::user()->isHotelBookingManager())
                             {{-- Hotel Manager Responsive Links --}}
                              <x-responsive-nav-link :href="route('hotelmanager.hotels.index')" :active="request()->routeIs('hotelmanager.hotels.*')"> {{ __('Your Hotels') }} </x-responsive-nav-link>
                              <x-responsive-nav-link :href="route('hotelmanager.hotel-rooms.index')" :active="request()->routeIs('hotelmanager.hotel-rooms.*')"> {{ __('Your Rooms') }} </x-responsive-nav-link>
                              <x-responsive-nav-link :href="route('hotelmanager.hotel-bookings.index')" :active="request()->routeIs('hotelmanager.hotel-bookings.*')"> {{ __('Hotel Bookings') }} </x-responsive-nav-link>

                         @elseif (Auth::user()->isVendor())
                             {{-- Vendor Responsive Links --}}
                              <x-responsive-nav-link :href="route('vendor.products.index')" :active="request()->routeIs('vendor.products.*')"> {{ __('Your Products') }} </x-responsive-nav-link>
                             <x-responsive-nav-link :href="route('vendor.product-orders.index')" :active="request()->routeIs('vendor.product-orders.*')"> {{ __('Your Orders') }} </x-responsive-nav-link>

                         @else
                             {{-- Other Roles Responsive Links --}}
                              @if (Auth::user()->isEmployee())
                                  <x-responsive-nav-link :href="route('employee.dashboard')" :active="request()->routeIs('employee.dashboard')"> {{ __('Employee Dashboard') }} </x-responsive-nav-link>
                             @elseif (Auth::user()->isArticleWriter())
                                  <x-responsive-nav-link :href="route('writer.dashboard')" :active="request()->routeIs('writer.dashboard')"> {{ __('Writer Dashboard') }} </x-responsive-nav-link>
                                  <x-responsive-nav-link :href="route('writer.articles.index')" :active="request()->routeIs('writer.articles.*')"> {{ __('Your Articles') }} </x-responsive-nav-link>
                             @else
                                  <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"> {{ __('User Dashboard') }} </x-responsive-nav-link>
                             @endif
                         @endif
                     @endauth {{-- End @auth for Responsive Links --}}
                </div>

                <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                    <div class="px-4">
                         @auth {{-- Check auth before accessing user properties --}}
                             <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->username }}</div>
                             <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                         @else {{-- Show different info if not logged in --}}
                             <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ __('Guest') }}</div>
                         @endauth
                    </div>
                     <div class="mt-3 space-y-1">
                          @auth {{-- Only show these links if logged in --}}
                             <x-responsive-nav-link :href="route('profile')"> {{ __('Profile') }} </x-responsive-nav-link>
                             <form method="POST" action="{{ route('logout') }}">
                                 @csrf
                                 <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                     {{ __('Log Out') }}
                                 </x-responsive-nav-link>
                             </form>
                         @else {{-- Show login/register links if logged out --}}
                             <x-responsive-nav-link :href="route('login')"> {{ __('Log In') }} </x-responsive-nav-link>
                             <x-responsive-nav-link :href="route('register')"> {{ __('Register') }} </x-responsive-nav-link>
                         @endauth
                     </div>
                </div>
             </div>

         </nav>


        <!-- Page Heading -->
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                @yield('header')
            </div>
        </header>


        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>