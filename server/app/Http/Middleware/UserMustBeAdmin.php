<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserMustBeAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (! \Auth::user()->isRole('admin')) {
            return redirect()->route('admin.dashboard')->with('alert', 'K této stránce nemáte přístup.');
        }

        return $next($request);
    }
}
