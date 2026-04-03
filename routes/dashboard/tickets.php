<?php

use App\Http\Controllers\Dashboard\MyTicketsController;
use Illuminate\Support\Facades\Route;

Route::prefix('tableau-de-bord/tickets')->name('dashboard.tickets.')->group(function () {
    Route::get('/', [MyTicketsController::class, 'index'])->name('index');
    Route::post('{id}/transfer', [MyTicketsController::class, 'transfer'])->name('transfer');
    Route::post('{id}/cancel', [MyTicketsController::class, 'cancel'])->name('cancel');
});
