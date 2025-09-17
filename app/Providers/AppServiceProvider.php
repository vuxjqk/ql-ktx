<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        Gate::define('is-super-admin', function (User $user) {
            return $user->role === 'super_admin';
        });

        Gate::define('is-admin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('is-staff', function (User $user) {
            return $user->role === 'staff';
        });

        Gate::define('is-student', function (User $user) {
            return $user->role === 'student';
        });
    }
}
