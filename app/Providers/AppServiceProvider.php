<?php

namespace App\Providers;

use App\Services\ApiService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ApiService::class, function () {
            return new ApiService;
        });
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $locale = request()->route('locale') ?? config('app.locale', 'fr');
            $view->with('locale', $locale);
        });
    }
}
