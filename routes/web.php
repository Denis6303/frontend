<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('ticketing.home');
})->name('home');

Route::prefix('ticketing')->name('ticketing.')->group(function () {
    Route::get('/', function () {
        return view('ticketing.home');
    })->name('home');

    Route::get('/events', function () {
        return view('ticketing.events');
    })->name('events');

    Route::get('/events/{id}', function ($id) {
        // TODO: Récupérer l’événement depuis le backend
        return view('ticketing.event-detail', ['id' => $id]);
    })->name('events.show');

    Route::get('/cart', function () {
        return view('ticketing.cart');
    })->name('cart');
});
