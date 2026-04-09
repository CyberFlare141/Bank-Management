<?php

namespace App\Providers;

use App\Support\AdminBootstrapper;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
        if (!app()->environment('local') || app()->runningUnitTests() || app()->runningInConsole()) {
            return;
        }

        try {
            if (!Schema::hasTable('users') || !Schema::hasTable('customers') || !Schema::hasTable('accounts')) {
                return;
            }

            app(AdminBootstrapper::class)->ensureDefaultAdmin();
        } catch (Throwable) {
            // Skip admin bootstrapping until the database is reachable and migrated.
        }
    }
}
