<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        Validator::extend('recaptcha', '\App\Validators\ReCaptcha@validate');
        View::share('recaptchaSiteKey', config('recaptcha.key'));
    }
}
