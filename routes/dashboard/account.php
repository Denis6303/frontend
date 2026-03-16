<?php

use Illuminate\Support\Facades\Route;

// Routes liées au compte utilisateur dans le dashboard.
// Sous-fichiers optionnels dans routes/dashboard/account/*.php

Route::prefix('tableau-de-bord/compte')->name('dashboard.account.')->group(function () {
    foreach (glob(__DIR__ . '/account/*.php') as $file) {
        require $file;
    }
});

