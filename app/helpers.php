<?php

use Illuminate\Support\Facades\Session;

if (! function_exists('votix_media_url')) {
    /**
     * Réécrit les URLs sous /storage/ pour utiliser l’origine de l’API (VOTIX_API_URL).
     * Évite les images cassées quand l’API a été générée avec un APP_URL différent de l’URL réelle du backend.
     */
    function votix_media_url(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        $url = trim($url);
        if ($url === '') {
            return null;
        }

        $base = rtrim((string) config('votix_api.base_url'), '/');
        $storagePrefix = '/storage/';

        if (str_starts_with($url, $storagePrefix)) {
            return $base.$url;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (is_string($path) && str_starts_with($path, $storagePrefix)) {
            $query = parse_url($url, PHP_URL_QUERY);
            $fragment = parse_url($url, PHP_URL_FRAGMENT);
            $built = $base.$path;
            if (is_string($query) && $query !== '') {
                $built .= '?'.$query;
            }
            if (is_string($fragment) && $fragment !== '') {
                $built .= '#'.$fragment;
            }

            return $built;
        }

        return $url;
    }
}

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
            return votix_media_url($fromApi !== '' ? $fromApi : null) ?? '';
        }

        $input = Session::getOldInput();
        if (! is_array($input) || ! array_key_exists('image_url', $input)) {
            return votix_media_url($fromApi !== '' ? $fromApi : null) ?? '';
        }

        $flashed = $input['image_url'];
        if (! is_string($flashed)) {
            return votix_media_url($fromApi !== '' ? $fromApi : null) ?? '';
        }

        $flashed = trim($flashed);
        $raw = $flashed !== '' ? $flashed : $fromApi;

        return votix_media_url($raw !== '' ? $raw : null) ?? '';
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

if (! function_exists('auth_user_display_name')) {
    /**
     * Nom affiché dans le header : prénom + nom (session API), sinon name, sinon email local.
     */
    function auth_user_display_name(): string
    {
        $sessionUser = Session::get(config('votix_api.session_user_key'));
        if (is_array($sessionUser)) {
            $fn = trim((string) ($sessionUser['first_name'] ?? ''));
            $ln = trim((string) ($sessionUser['last_name'] ?? ''));
            $full = trim($fn.' '.$ln);
            if ($full !== '') {
                return $full;
            }
            $name = trim((string) ($sessionUser['name'] ?? ''));
            if ($name !== '') {
                return $name;
            }
            $email = $sessionUser['email'] ?? null;
            if (is_string($email) && $email !== '') {
                $parts = explode('@', $email, 2);

                return $parts[0] !== '' ? $parts[0] : '—';
            }
        }

        $u = auth()->user();
        if ($u !== null) {
            $fn = trim((string) data_get($u, 'first_name', ''));
            $ln = trim((string) data_get($u, 'last_name', ''));
            $full = trim($fn.' '.$ln);
            if ($full !== '') {
                return $full;
            }

            return trim((string) ($u->name ?? $u->email ?? '')) ?: '—';
        }

        return '—';
    }
}

if (! function_exists('auth_user_email')) {
    /**
     * Email de l’utilisateur connecté (session API ou modèle local).
     */
    function auth_user_email(): ?string
    {
        $sessionUser = Session::get(config('votix_api.session_user_key'));
        if (is_array($sessionUser) && isset($sessionUser['email']) && is_string($sessionUser['email']) && $sessionUser['email'] !== '') {
            return $sessionUser['email'];
        }

        return auth()->user()?->email;
    }
}
