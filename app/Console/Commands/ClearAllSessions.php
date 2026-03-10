<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ClearAllSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:clear-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all frontend sessions, cached API tokens and reset authenticated state';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Clearing frontend sessions and Votix API tokens...');

        $driver = Config::get('session.driver');

        if ($driver === 'file') {
            $sessionPath = storage_path('framework/sessions');
            if (File::exists($sessionPath)) {
                File::cleanDirectory($sessionPath);
                $this->line(" - File sessions cleared in: {$sessionPath}");
            }
        } elseif ($driver === 'database') {
            $table = Config::get('session.table', 'sessions');
            DB::table($table)->truncate();
            $this->line(" - Database sessions table truncated: {$table}");
        } else {
            // Fallback: use Laravel's cache/session clear commands where relevant
            Artisan::call('cache:clear');
            $this->line(' - Cache cleared via cache:clear (non file/database session driver).');
        }

        // Clear cached client token for the Votix API
        $clientTokenKey = config('votix_api.client_token_cache_key');
        if ($clientTokenKey) {
            Cache::forget($clientTokenKey);
            $this->line(" - Votix API client token cache key cleared: {$clientTokenKey}");
        }

        // It is not possible to clear browser storage (localStorage, cookies) from an Artisan command.
        // Those will be dropped on next page load when backend no longer recognises any session.

        $this->info('Frontend session reset complete. Users will be treated as logged out on next request.');

        return self::SUCCESS;
    }
}

