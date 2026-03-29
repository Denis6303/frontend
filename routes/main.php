<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\EventDraftController;
use App\Http\Controllers\Dashboard\MyEventController;
use App\Http\Controllers\Ticketing\EventController as PublicEventController;
use App\Http\Controllers\Ticketing\OrderIntentController;
use Illuminate\Support\Facades\Route;

// Changement de langue (sans préfixe locale)
Route::get('/locale/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])
    ->name('locale.switch')
    ->where(['locale' => 'fr|en']);

// Groupe principal avec locale dans l'URL
Route::middleware('setlocale')->prefix('{locale}')->where(['locale' => 'fr|en'])->group(function () {
    // Auth (routes détaillées dans routes/auth.php)
    require __DIR__ . '/auth.php';

    // Home publique
    Route::get('/', [PublicEventController::class, 'home'])->name('home');

    // Ticketing (liste + ancien détail)
    Route::prefix('ticketing')->name('ticketing.')->group(function () {
        Route::get('/', [PublicEventController::class, 'home'])->name('index');
        Route::get('/events', [PublicEventController::class, 'index'])->name('events');
        Route::get('/events/{id}', [PublicEventController::class, 'showLegacy'])->name('events.show.legacy');
        Route::get('/cart', function (string $locale) {
            return view('ticketing.cart');
        })->name('cart');

        Route::middleware(['auth'])->prefix('checkout')->name('checkout.')->group(function () {
            Route::post('/prepare', [OrderIntentController::class, 'prepare'])->name('prepare');
            Route::get('/{key}/return', [OrderIntentController::class, 'returnPage'])
                ->where('key', '[0-9a-fA-F-]{36}')
                ->name('return');
            Route::post('/{key}/cancel', [OrderIntentController::class, 'cancel'])
                ->where('key', '[0-9a-fA-F-]{36}')
                ->name('cancel');
            Route::post('/{key}/pay', [OrderIntentController::class, 'pay'])
                ->where('key', '[0-9a-fA-F-]{36}')
                ->name('pay');
            Route::get('/{key}', [OrderIntentController::class, 'show'])
                ->where('key', '[0-9a-fA-F-]{36}')
                ->name('show');
        });
    });

    // Nouveau détail public : /{locale}/evenements/{slug}
    Route::get('/evenements/{slug}', [PublicEventController::class, 'show'])
        ->name('events.show');

    // Tableau de bord (utilisateur connecté) : routes/dashboard/*.php
    // Chargées automatiquement sous middleware `auth` (voir prompt).
    Route::middleware(['auth'])->group(function () {
        foreach (glob(base_path('routes/dashboard/*.php')) as $routeFile) {
            require $routeFile;
        }
    });

    // Exemple page contact (à créer)
    Route::get('/nous-contacter', function (string $locale) {
        return view('pages.contact');
    })->name('contact');
}
);

