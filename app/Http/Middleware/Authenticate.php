<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * On considère l'utilisateur "auth" si le token API est en session.
     * Cela permet d'utiliser le middleware `auth` (routes/dashboard) avec l'auth existante.
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        if (session(config('votix_api.session_access_token_key'))) {
            return $next($request);
        }

        return parent::handle($request, $next, ...$guards);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        if ($request->isMethod('get')) {
            $request->session()->put('url.intended', $request->fullUrl());
        }

        $locale = $request->route('locale', app()->getLocale() ?? 'fr');

        return route('login', ['locale' => $locale]);
    }
}
