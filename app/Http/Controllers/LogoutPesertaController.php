<?php

namespace App\Http\Controllers;


class LogoutPesertaController extends Controller
{
    public function __invoke()
    {
        auth()->guard('peserta')->logout();
        session()->flush();

        return redirect(route('peserta.login'));
    }
}
