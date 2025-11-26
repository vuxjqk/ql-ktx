<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceCorrectUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Force HTTPS nếu bật trong .env
        if (env('FORCE_HTTPS', false)) {
            $request->server->set('HTTPS', 'on');
            URL::forceScheme('https');
        }

        // 2. Force root URL (bắt buộc với devtunnels.ms)
        if ($forceRoot = env('APP_URL', null)) {
            URL::forceRootUrl(rtrim($forceRoot, '/'));
        }

        return $next($request);
    }
}
