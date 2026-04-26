<?php

namespace App\Providers;

use App\Services\ApiService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('helpers.php');

        $this->app->singleton(ApiService::class, function () {
            return new ApiService;
        });
    }

    public function boot(): void
    {
        $this->ensureRuntimeDirectories();

        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            $locale = request()->route('locale') ?? config('app.locale', 'fr');
            $view->with('locale', $locale);
        });

        View::composer('dashboard.events.draft.create-step1', function ($view) {
            $api = app(ApiService::class);
            $items = $api->getData('categories', [], true, 'items', true);
            $view->with('categories', is_array($items) ? $items : []);
        });
    }

    private function ensureRuntimeDirectories(): void
    {
        $dirs = [
            storage_path('framework'),
            storage_path('framework/cache'),
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('framework/testing'),
            storage_path('logs'),
        ];

        foreach ($dirs as $dir) {
            if (! File::exists($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }
        }
    }
}
