<?php

use App\Http\Controllers\Dashboard\EventDraftController;
use App\Http\Controllers\Dashboard\MyEventController;
use Illuminate\Support\Facades\Route;

// Liste des événements (dashboard)
Route::get('/', [MyEventController::class, 'index'])->name('index');

// Création d'événement (brouillon) - 4 étapes (affichage formulaires)
Route::prefix('brouillon/creer')->name('draft.create.')->group(function () {
    Route::get('/etape-1', function (string $locale) {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }
        return view('dashboard.events.draft.create-step1', ['locale' => $locale]);
    })->name('step1');

    Route::get('/etape-2', function (string $locale) {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }
        return view('dashboard.events.draft.create-step2', ['locale' => $locale]);
    })->name('step2');

    Route::get('/etape-3', function (string $locale) {
        if (! session(config('votix_api.session_access_token_key'))) {
            return redirect()->route('login', ['locale' => $locale]);
        }
        return view('dashboard.events.draft.create-step3', ['locale' => $locale]);
    })->name('step3');

    Route::get('/etape-4', [EventDraftController::class, 'showStep4'])->name('step4');
});

// Création d'événement (brouillon) - 4 étapes (soumission vers API)
Route::prefix('brouillon/creer')->name('draft.create.')->group(function () {
    Route::post('/etape-1', [EventDraftController::class, 'storeStep1'])->name('step1.store');
    Route::post('/etape-2', [EventDraftController::class, 'storeStep2'])->name('step2.store');
    Route::post('/etape-3', [EventDraftController::class, 'storeStep3'])->name('step3.store');
    Route::post('/etape-4/finaliser', [EventDraftController::class, 'finalize'])->name('step4.finalize');
});

