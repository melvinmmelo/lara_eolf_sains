<?php

namespace App\Providers;

use App\Models\Branches;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

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
        //
        Schema::defaultStringLength(191);

        // Force HTTPS for all URLs in production
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        view()->composer('*', function ($view) {
            $gbranches = Branches::all(); // Get all branches from the Branch model

            $branchName = '';

            if(Session::has('branch_code')) {

                $branch = Branches::where('code', '=', trim(Session::get('branch_code')))->first();
                $branchName = $branch->name;
            }

            $view->with('gbranches', $gbranches)->with('branch_name', $branchName);
        });

        Gate::define('admin', function ($user) {
            return $user->hasRole('admin');
        });
    }
}
