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

        if ( isset( $_SERVER['HTTP_ORIGIN'] ) ) {
            $next->header('Access-Control-Allow-Origin' , $_SERVER['HTTP_ORIGIN']);
            $next->header('Access-Control-Allow-Credentials' , 'true');
        } else {
            $next->header('Access-Control-Allow-Origin' , '*');
        }

        $next->header('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $next->header('Access-Control-Allow-Headers', 'Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, Cache-Control, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, Accept-Encoding');

        return $next;
    }
}
