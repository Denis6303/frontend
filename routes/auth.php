<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Ces routes sont incluses à l'intérieur du groupe {locale} dans routes/main.php

Route::get('/login', fn (string $locale) => view('pages.auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/auth/{provider}/redirect', [AuthController::class, 'socialRedirect'])
    ->where('provider', 'google|tiktok')
    ->name('auth.social.redirect');
Route::get('/auth/{provider}/callback', [AuthController::class, 'socialCallback'])
    ->where('provider', 'google|tiktok')
    ->name('auth.social.callback');

Route::get('/register', fn (string $locale) => view('pages.auth.register'))->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', fn (string $locale) => view('pages.auth.forgot-password'))->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendForgotPassword'])->name('password.email');

Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'submitResetPassword'])->name('password.update');

Route::get('/email-verified', [AuthController::class, 'showEmailVerified'])->name('email.verified');
Route::post('/email/resend', [AuthController::class, 'resendVerification'])->name('verification.resend');

