<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use MongoDB\Driver\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        if (request('change_language')) {
            session()->put('language', request('change_language'));
            $language = request('change_language');
        } elseif (session('language')) {
            $language = session('language');
        } elseif (config('panel.primary_language')) {
            $language = config('panel.primary_language');
        }

        if (isset($language)) {
            app()->setLocale($language);
        }

        return $next($request);

/*        if(Session()->has('applocale') AND array_key_exists(Session()->get('applocale'), config('languages'))){
            App::setLocale(Session()->get('applocale'));
        }else{
            App::setLocale(config('app.fallback_locale'));
        }
        return $next($request);*/
    }
}
