<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supported = ['fr', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if ($locale && in_array($locale, $this->supported)) {
            app()->setLocale($locale);
        } else {
            app()->setLocale(config('app.locale', 'fr'));
        }

        return $next($request);
    }
}
