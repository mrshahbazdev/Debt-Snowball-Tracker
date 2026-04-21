<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public const SUPPORTED = ['en', 'de'];

    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale');
        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = config('app.locale', 'en');
        }
        App::setLocale($locale);

        return $next($request);
    }
}
