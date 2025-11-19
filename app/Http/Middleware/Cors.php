<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // === XỬ LÝ OPTIONS (PREFlight) ===
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 200);
        } else {
            $response = $next($request);
        }

        // === CORS HEADERS ===
        $origin = $request->headers->get('Origin');
        $response->headers->set('Access-Control-Allow-Origin', $origin ?: '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', '*');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '86400');
        $response->headers->set('Access-Control-Expose-Headers', '*');

        // === TỰ ĐỘNG BỎ QUA CSRF CHO API THANH TOÁN (KHÔNG CẦN SỬA SAU NÀY) ===
        $path = $request->path();
        $isPaymentApi =
            str_contains($path, 'payment') ||
            str_contains($path, 'pay') ||
            str_contains($path, 'callback') ||
            str_contains($path, 'ipn') ||
            str_contains($path, 'webhook') ||
            str_contains($path, 'return') ||
            $request->headers->has('X-Payment-Gateway') ||
            ($request->isJson() && $origin === null && $request->isMethod('POST'));

        if ($isPaymentApi) {
            // Trick: Laravel skip CSRF nếu method là GET
            $request->server->set('REQUEST_METHOD', 'GET');
        }

        return $response;
    }
}
