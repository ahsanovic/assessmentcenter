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
            'tes-potensi/motivasi-komitmen/*',
            'tes-potensi/berpikir-kritis/*',
            'tes-potensi/problem-solving/*',
            'tes-potensi/kesadaran-diri/*',
            'tes-potensi/kuesioner',
            'tes-potensi/hasil-nilai',
            'tes-cakap-digital/home',
            'tes-cakap-digital/ujian/*',
            'tes-cakap-digital/hasil',
            'tes-kompetensi-teknis/home',
            'tes-kompetensi-teknis/ujian/*',
            'tes-kompetensi-teknis/hasil',
            'tes-intelektual/home',
            'tes-intelektual/subtes1/*',
            'tes-intelektual/subtes2/*',
            'tes-intelektual/subtes3/*',
            'tes-intelektual/hasil-nilai',
            'tes-pspk/home',
            'tes-pspk/ujian/*',
            'tes-pspk/hasil',
        ];

        $prefix = $request->segment(1);

        if (session()->has('exam_pin')) {
            foreach ($allowed_urls as $url) {
                if ($request->is($url)) {
                    return $next($request);
                }
            }

            // Jika mencoba mengakses halaman selain home, redirect ke home
            return redirect()->route("peserta.$prefix.home");
        }

        $examRoutes = [
            'tes-potensi',
            'tes-intelektual',
            'tes-cakap-digital',
            'tes-kompetensi-teknis',
            'tes-pspk',
        ];

        if (in_array($request->path(), $examRoutes, true)) {
            return $next($request);
        }

        // if ($request->is('tes-potensi') || $request->is('tes-intelektual') || $request->is('tes-cakap-digital') || $request->is('tes-kompetensi-teknis')) {
        //     return $next($request);
        // }

        // $prefix = $request->segment(1);
        return redirect()->route("peserta.$prefix");
    }
}
