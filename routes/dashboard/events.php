<?php

use Illuminate\Support\Facades\Route;

// Toutes les routes liées aux événements du dashboard.
// On charge automatiquement tous les sous-fichiers de routes/dashboard/events/*.php

Route::prefix('tableau-de-bord/evenements')->name('dashboard.events.')->group(function () {
    foreach (glob(__DIR__ . '/events/*.php') as $file) {
        require $file;
    }
});

