<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Fichier d'entrée principal des routes web
| - Ne contient que des inclusions de fichiers spécialisés
|--------------------------------------------------------------------------
*/

// Routes publiques principales (site front, home, pages, etc.)
require __DIR__ . '/main.php';

// Authentification (complément si besoin)
// Les routes d'auth avec locale sont définies dans main.php via require auth.php

// Paiements événements
require __DIR__ . '/payment.php';

// Routes validator (scan de tickets, etc.)
require __DIR__ . '/validator.php';

// Redirections globales, liens techniques, etc.
require __DIR__ . '/redirects.php';

