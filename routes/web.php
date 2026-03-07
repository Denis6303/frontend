<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

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

Route::middleware('setlocale')->prefix('{locale}')->where(['locale' => 'fr|en'])->group(function () {
    Route::get('/', function (string $locale) {
        App::setLocale($locale);
        return view('ticketing.home');
    })->name('home');

    // Auth
    Route::get('/login', function (string $locale) {
        return view('auth.login');
    })->name('login');
    Route::get('/register', function (string $locale) {
        return view('auth.register');
    })->name('register');
    Route::get('/forgot-password', function (string $locale) {
        return view('auth.forgot-password');
    })->name('password.request');
    Route::post('/forgot-password', function () {
        return back()->with('status', __('auth.reset_link_sent'));
    })->name('password.email');
    Route::post('/login', function () {
        return redirect()->route('home', ['locale' => request()->route('locale')]);
    });
    Route::post('/register', function () {
        return redirect()->route('home', ['locale' => request()->route('locale')]);
    });

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

    // Exemple page contact (à créer)
    Route::get('/nous-contacter', function (string $locale) {
        return view('pages.contact');
    })->name('contact');
});
