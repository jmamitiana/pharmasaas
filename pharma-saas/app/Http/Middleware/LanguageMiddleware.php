<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class LanguageMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');
        
        if (!$locale && auth()->check()) {
            $tenantId = auth()->user()->tenant_id;
            $locale = Setting::getValue('language', 'fr', $tenantId);
        }

        if (!$locale) {
            $locale = config('app.fallback_locale', 'fr');
        }

        if (!in_array($locale, ['fr', 'en'])) {
            $locale = 'fr';
        }

        app()->setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}
