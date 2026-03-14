<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\EventDraftController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (with locale: /fr/... or /en/...)
|--------------------------------------------------------------------------
*/

// Redirection racine vers la langue par défaut
Route::get('/', function () {
    return redirect()->route('home', ['locale' => 'fr']);
});

Route::get('/locale/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])
    ->name('locale.switch')
    ->where(['locale' => 'fr|en']);

// Backend redirects (no locale in URL)
Route::get('/email-verified', [AuthController::class, 'showEmailVerified'])->name('email.verified.plain');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset.plain');

Route::middleware('setlocale')->prefix('{locale}')->where(['locale' => 'fr|en'])->group(function () {
    Route::get('/', function (string $locale) {
        App::setLocale($locale);
        return view('ticketing.home');
    })->name('home');

    // Auth
    Route::get('/login', fn (string $locale) => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', fn (string $locale) => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/forgot-password', fn (string $locale) => view('auth.forgot-password'))->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendForgotPassword'])->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'submitResetPassword'])->name('password.update');
    Route::get('/email-verified', [AuthController::class, 'showEmailVerified'])->name('email.verified');
    Route::post('/email/resend', [AuthController::class, 'resendVerification'])->name('verification.resend');

    // Ticketing
    Route::prefix('ticketing')->name('ticketing.')->group(function () {
        Route::get('/', function (string $locale) {
            return view('ticketing.home');
        })->name('index');
        Route::get('/events', function (string $locale) {
            return view('ticketing.events');
        })->name('events');
        Route::get('/events/{id}', function (string $locale, $id) {
            return view('ticketing.event-detail', ['id' => $id]);
        })->name('events.show');
        Route::get('/cart', function (string $locale) {
            return view('ticketing.cart');
        })->name('cart');
    });

    // Tableau de bord (protégé : redirection vers login si non connecté)
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', function (string $locale) {
            if (!session(config('votix_api.session_access_token_key'))) {
                return redirect()->route('login', ['locale' => $locale]);
            }
            return view('dashboard.main.index', ['locale' => $locale]);
        })->name('index');

        Route::get('/events', function (string $locale) {
            if (!session(config('votix_api.session_access_token_key'))) {
                return redirect()->route('login', ['locale' => $locale]);
            }
            return view('dashboard.main.events', ['locale' => $locale]);
        })->name('events');

        Route::get('/about', function (string $locale) {
            if (!session(config('votix_api.session_access_token_key'))) {
                return redirect()->route('login', ['locale' => $locale]);
            }
            return view('dashboard.main.about', ['locale' => $locale]);
        })->name('about');

        // Création d'événement (brouillon) - 4 étapes (affichage formulaires)
        Route::prefix('events/draft/create')->name('events.draft.create.')->group(function () {
            Route::get('/step-1', function (string $locale) {
                if (!session(config('votix_api.session_access_token_key'))) {
                    return redirect()->route('login', ['locale' => $locale]);
                }
                return view('dashboard.events.draft.create-step1', ['locale' => $locale]);
            })->name('step1');

            Route::get('/step-2', function (string $locale) {
                if (!session(config('votix_api.session_access_token_key'))) {
                    return redirect()->route('login', ['locale' => $locale]);
                }
                return view('dashboard.events.draft.create-step2', ['locale' => $locale]);
            })->name('step2');

            Route::get('/step-3', function (string $locale) {
                if (!session(config('votix_api.session_access_token_key'))) {
                    return redirect()->route('login', ['locale' => $locale]);
                }
                return view('dashboard.events.draft.create-step3', ['locale' => $locale]);
            })->name('step3');

            Route::get('/step-4', function (string $locale) {
                if (!session(config('votix_api.session_access_token_key'))) {
                    return redirect()->route('login', ['locale' => $locale]);
                }
                return view('dashboard.events.draft.create-step4', ['locale' => $locale]);
            })->name('step4');
        });

        // Création d'événement (brouillon) - 4 étapes (soumission vers API)
        Route::prefix('events/draft/create')->name('events.draft.create.')->group(function () {
            Route::post('/step-1', [EventDraftController::class, 'storeStep1'])->name('step1.store');
            Route::post('/step-2', [EventDraftController::class, 'storeStep2'])->name('step2.store');
            Route::post('/step-3', [EventDraftController::class, 'storeStep3'])->name('step3.store');
            Route::post('/step-4/finalize', [EventDraftController::class, 'finalize'])->name('step4.finalize');
        });
    });

    // Exemple page contact (à créer)
    Route::get('/nous-contacter', function (string $locale) {
        return view('pages.contact');
    })->name('contact');
});
