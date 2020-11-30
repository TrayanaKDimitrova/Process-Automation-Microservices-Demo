<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $next = $next($request);
        if (!method_exists($next, 'header')) {
            return $next;
        }

        $adminService = env('ADMIN_URL', 'null');
        $clientService = env('CLIENT_URL', 'null');
        if ( isset( $_SERVER['HTTP_ORIGIN'] ) ) {
            if (strstr($_SERVER['HTTP_ORIGIN'], $clientService) !== false) {
                $next->header('Access-Control-Allow-Origin' , $clientService);
                $next->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
            } elseif (strstr($_SERVER['HTTP_ORIGIN'], $adminService) !== false) {
                $next->header('Access-Control-Allow-Origin' , $adminService);
                $next->header('Access-Control-Allow-Methods', 'POST, OPTIONS');
            }
        }

        $next->header('Access-Control-Allow-Credentials' , 'true');
        $next->header('Access-Control-Allow-Headers', 'Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, Cache-Control, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, Accept-Encoding');

        return $next;
    }
}
