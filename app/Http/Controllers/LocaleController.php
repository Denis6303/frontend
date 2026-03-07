<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, ['fr', 'en'])) {
            $locale = 'fr';
        }

        $referer = $request->header('referer');
        $path = $referer ? parse_url($referer, PHP_URL_PATH) : '';
        $path = $path ? preg_replace('#^/(fr|en)(/|$)#', '/' . $locale . '$2', $path) : '/' . $locale;

        return Redirect::to($path ?: '/' . $locale);
    }
}
