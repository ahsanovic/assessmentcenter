<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortofolioAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $metode_tes = auth()->guard('peserta')->user()->event->metode_tes_id;

        if ($metode_tes !== 1) {
            return redirect()->route('peserta.dashboard');
        }

        return $next($request);
    }
}
