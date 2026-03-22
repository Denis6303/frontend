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

if (! function_exists('display_currency_label')) {
    /**
     * Libellé affiché pour la devise (API souvent en XOF, affichage utilisateur en FCFA).
     */
    function display_currency_label(?string $code): string
    {
        $code = strtoupper(trim((string) $code));
        if ($code === '') {
            return '';
        }

        return $code === 'XOF' ? 'FCFA' : $code;
    }
}

if (! function_exists('public_event_dates_count')) {
    /**
     * Nombre de dates / sessions d’un événement public (liste, carte, détail).
     * S’appuie sur occurrences[].start_date ou start_dates[].
     */
    function public_event_dates_count(array $event): int
    {
        $occ = $event['occurrences'] ?? null;
        if (is_array($occ) && $occ !== []) {
            if (array_is_list($occ)) {
                $n = count(array_filter($occ, static function ($o) {
                    return is_array($o) && ! empty($o['start_date'] ?? null);
                }));
                if ($n > 0) {
                    return $n;
                }
            }
        }

        $starts = $event['start_dates'] ?? null;
        if (is_array($starts) && $starts !== []) {
            return count(array_filter($starts, static function ($d) {
                return $d !== null && $d !== '';
            }));
        }

        if (! empty($event['occurrences'][0]['start_date'] ?? null)) {
            return 1;
        }

        if (! empty($event['start_date'] ?? null)) {
            return 1;
        }

        return 0;
    }
}
