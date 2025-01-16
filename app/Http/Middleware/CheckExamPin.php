<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckExamPin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowed_urls = [
            'tes-potensi/home',
            'tes-potensi/interpersonal/*',
            'tes-potensi/pengembangan-diri/*',
            'tes-potensi/kecerdasan-emosi/*',
        ];

        if (session()->has('exam_pin')) {
            foreach ($allowed_urls as $url) {
                if ($request->is($url)) {
                    return $next($request);
                }
            }

            // Jika mencoba mengakses halaman selain home, redirect ke home
            return redirect()->route('peserta.tes-potensi.home');
        }

        if ($request->is('tes-potensi')) {
            return $next($request);
        }

        return redirect()->route('peserta.tes-potensi');
    }

}