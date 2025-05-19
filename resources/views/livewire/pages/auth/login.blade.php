<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // ** تأكد من استيراد Auth Facade **
use App\Models\User; // Import the User model (still useful if you need to eager load relations later, but not strictly needed for Auth::user()->user_type)


new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request (Web Login).
     */
    public function login(): void
    {
        $this->validate(); // Validates the form object properties ($this->form->email, $this->form->password)

        $this->form->authenticate(); // Attempts authentication using Laravel's built-in Auth. Throws exception on failure.

        // If authenticate() did NOT throw an exception, authentication was successful.
        // Now, get the authenticated user using the Auth facade.
        $authenticatedUser = Auth::user();

        // Check if user is not null (should not happen if authenticate() succeeded, but good practice)
        if (!$authenticatedUser) {
            // This case is unlikely if authenticate() passed, but handle defensively
             Log::error('Authentication succeeded via LoginForm::authenticate but Auth::user() is null.');
             throw ValidationException::withMessages([
                 'form.email' => [__('Authentication failed unexpectedly.')],
             ]);
        }


        Session::regenerate(); // Regenerate the session ID for security

        // Redirect based on user type
        $redirectPath = match ($authenticatedUser->user_type) { // ** تم التعديل هنا لاستخدام $authenticatedUser->user_type **
             'Admin' => route('admin.dashboard', absolute: false),
             'HotelBookingManager' => route('hotelmanager.dashboard', absolute: false),
             'Vendor' => route('vendor.dashboard', absolute: false),
             default => route('dashboard', absolute: false), // Default redirect for Tourist or others
         };

        $this->redirect($redirectPath, navigate: true);
    }

    /**
     * Handle quick filling of login credentials for seeded users.
     * Note: This fills the web form fields. It does NOT perform API login.
     *
     * @param string $email
     * @param string $password (plain text password used in seeder)
     */
    public function quickFillLogin(string $email, string $password): void
    {
        $this->form->email = $email; // Fill the email field
        $this->form->password = $password; // Fill the password field

        // Optional: You could uncomment the line below to automatically attempt login after filling
        // $this->login();
    }

    /**
     * Process JSON input (for API testing demonstration - NOT a standard web login method).
     * This is mainly for showcasing how to handle JSON input in Livewire/Volt
     * and might be used in combination with client-side JS for API calls.
     *
     * @param string $jsonData
     */
    public function processJson(string $jsonData): void
    {
        // ... (الكود هنا كما هو لم يتغير، فهو متعلق بمعالجة JSON التي هي ميزة إضافية للتطوير) ...
         try {
            $data = json_decode($jsonData, true); // Decode the JSON string

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON data.');
            }

            Log::info('Received JSON data for API testing:', $data);

            Session::flash('json_status_success', 'JSON data processed successfully (logged). You can now use client-side JS to send it to your API.');

        } catch (\Exception $e) {
            Log::error('Error processing JSON data:', ['error' => $e->getMessage()]);
            Session::flash('json_status_error', 'Error processing JSON data: ' . $e->getMessage());
        }
        // ...
    }

}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address (or Username depending on LoginForm implementation) -->
        <div>
            {{-- The LoginForm validates 'email'. If you need username/email login for web, --}}
            {{-- you might need to customize LoginForm::authenticate() or the validation rules. --}}
            {{-- For now, assume this field accepts email and LoginForm handles it. --}}
            <x-input-label for="email" :value="__('Email')" /> {{-- Reverted Label to 'Email' for clarity, as LoginForm expects 'email' --}}
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" /> {{-- Reverted ID/Name/Type to standard email input --}}
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Quick Login Buttons -->
    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">{{ __('Quick Login (Seeded Users)') }}</h4>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <button type="button"
                    wire:click="quickFillLogin('admin@app.com', 'password')"
                    class="w-full px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('Admin') }} (admin@app.com)
            </button>
            <button type="button"
                    wire:click="quickFillLogin('tourist1@app.com', 'password')"
                    class="w-full px-4 py-2 text-xs font-semibold text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus::ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('Tourist') }} (tourist1@app.com)
            </button>
            <button type="button"
                    wire:click="quickFillLogin('vendor1@app.com', 'password')"
                    class="w-full px-4 py-2 text-xs font-semibold text-white bg-purple-600 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('Vendor') }} (vendor1@app.com)
            </button>
             <button type="button"
                    wire:click="quickFillLogin('hotelmanager1@app.com', 'password')"
                    class="w-full px-4 py-2 text-xs font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('Hotel Manager') }} (hotelmanager1@app.com)
            </button>
        </div>
    </div>

     <!-- JSON Input Area for API Testing (Development Use Only) -->
    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
         <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">{{ __('API JSON Input (Development)') }}</h4>
         {{-- Add a property in the Volt class if you want to bind this textarea --}}
         <x-input-label for="json_data" :value="__('Paste JSON Payload Here')" class="mb-1"/>
         {{-- You might need a Livewire property like 'public string $jsonData = '';' in the PHP part --}}
         <textarea id="json_data"
                   class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                   rows="6"
                   placeholder='Paste your JSON payload here, e.g.:{"username": "testuser", "email": "test@example.com", "password": "password", "password_confirmation": "password", "user_type": "Tourist"}'
                   {{-- You would typically add wire:model="jsonData" if you defined $jsonData property --}}
                   {{-- wire:model="jsonData" --}}
         ></textarea>
         {{-- Add a button to trigger JSON processing if using wire:model --}}
         {{-- <x-primary-button wire:click="processJson({{ $jsonData }})" class="mt-2">Process JSON (Log)</x-primary-button> --}}
         {{-- Display session flashes for JSON processing feedback --}}
         @if (Session::has('json_status_success'))
             <div class="mt-3 text-sm text-green-600 dark:text-green-400">{{ Session::get('json_status_success') }}</div>
         @endif
         @if (Session::has('json_status_error'))
             <div class="mt-3 text-sm text-red-600 dark:text-red-400">{{ Session::get('json_status_error') }}</div>
         @endif
         <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Note: Processing JSON here logs/validates it. Sending to API requires client-side JavaScript.</p>
     </div>

</div>