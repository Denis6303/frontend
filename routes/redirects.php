<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Redirection racine vers la langue par défaut
Route::get('/', function () {
    return redirect()->route('home', ['locale' => 'fr']);
});

// Backend redirects (sans locale dans l'URL)
Route::get('/email-verified', [AuthController::class, 'showEmailVerified'])->name('email.verified.plain');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset.plain');

