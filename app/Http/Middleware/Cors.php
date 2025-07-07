<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        return $response->header('Access-Control-Allow-Origin', 'https://troikatech.ai')
                        ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
    }
}
