<?php

use App\Http\Controllers\Dashboard\EventDraftController;
use App\Http\Controllers\Dashboard\MyEventController;
use Illuminate\Support\Facades\Route;

// Ces routes sont chargées à l'intérieur du groupe {locale} + middleware('auth') dans routes/main.php

Route::prefix('tableau-de-bord')->name('dashboard.')->group(function () {
    // Point d'entrée du dashboard
    Route::get('/', function (string $locale) {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }
        return view('dashboard.main.index', ['locale' => $locale]);
    })->name('home');

    // (temp) page compte
    Route::get('/mon-compte', function (string $locale) {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }
        return view('dashboard.main.about', ['locale' => $locale]);
    })->name('account');
});

