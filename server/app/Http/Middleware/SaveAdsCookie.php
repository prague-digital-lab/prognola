<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SaveAdsCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->input('gclid')) {
            $time = time() + 60 * 60 * 24 * 30; // Month

            return $next($request)->cookie('vb_aw', $request->input('gclid'), $time);
        }

        return $next($request);
    }
}
