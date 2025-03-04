<?php

namespace Vanguard\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIfAdminApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        if (auth()->check() && !auth()->user()->is_approved) {
//            auth()->logout();
//            return redirect('/')->with('error', 'Your account is not approved yet.');
//        }

        return $next($request);
    }
}
