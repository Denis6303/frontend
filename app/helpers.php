<?php

use Illuminate\Support\Facades\Session;

if (! function_exists('old_or_prefill')) {
    /**
     * Valeur d’affichage : ancienne saisie seulement si la clé existe dans l’input flashé
     * (évite qu’un old('title') vide écrase le préremplissage depuis l’API).
     */
    function old_or_prefill(string $key, mixed $prefill = null): mixed
    {
        if (! Session::hasOldInput()) {
            return $prefill;
        }

        $input = Session::getOldInput();
        if (! is_array($input) || ! array_key_exists($key, $input)) {
            return $prefill;
        }

        return $input[$key];
    }
}

if (! function_exists('banner_display_url_for_draft')) {
    /**
     * URL affichée pour la bannière (étape 1).
     * Si l’input flashé contient image_url vide, ne pas écraser l’URL issue du GET brouillon
     * (sinon preview bloquée : src vide + pas de classe .has-img alors que le résumé affiche bien l’image).
     */
    function banner_display_url_for_draft(?array $prefill): string
    {
        $fromApi = trim((string) (is_array($prefill) ? ($prefill['cover_url'] ?? '') : ''));

        if (! Session::hasOldInput()) {
            return $fromApi;
        }

        $input = Session::getOldInput();
        if (! is_array($input) || ! array_key_exists('image_url', $input)) {
            return $fromApi;
        }

        $flashed = $input['image_url'];
        if (! is_string($flashed)) {
            return $fromApi;
        }

        $flashed = trim($flashed);

        return $flashed !== '' ? $flashed : $fromApi;
    }
}
