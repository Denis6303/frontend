<?php

use App\Http\Controllers\Dashboard\MyTicketsController;
use Illuminate\Support\Facades\Route;

Route::prefix('tableau-de-bord/tickets')->name('dashboard.tickets.')->group(function () {
    Route::get('/', [MyTicketsController::class, 'index'])->name('index');
});
