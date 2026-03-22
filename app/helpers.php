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
