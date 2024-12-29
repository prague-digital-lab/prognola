<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class SaveUuidCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->exists('uuid')) {
            return $next($request);
        } else {

            $uuid = Uuid::v4();
            $request->session()->put('uuid', $uuid);

            return $next($request);

        }
    }
}
