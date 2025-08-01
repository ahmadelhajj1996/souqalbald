<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->header('Accept-Language', 'ar');

        if (! in_array($lang, ['en', 'ar'])) {
            $lang = 'ar';
        }

        App::setLocale($lang);

        return $next($request);
    }
}
