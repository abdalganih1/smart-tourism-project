<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate; // Import Gate Facade
use App\Models\User; // Import User model
class AppServiceProvider extends ServiceProvider
{

    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define a gate for accessing the admin panel
        Gate::define('access-admin-panel', function (User $user) {
            return $user->user_type === 'Admin';
        });

        // You can define more gates for specific roles/permissions if needed
        // Gate::define('isVendor', function (User $user) { return $user->user_type === 'Vendor'; });
        // Gate::define('isHotelBookingManager', function (User $user) { return $user->user_type === 'HotelBookingManager'; });
        // ...
    }
}
