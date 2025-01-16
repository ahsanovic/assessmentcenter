<?php

namespace App\Http\Controllers;


class LogoutAssessorController extends Controller
{
    public function __invoke()
    {
        auth()->guard('assessor')->logout();
        session()->flush();

        return redirect(route('assessor.login'));
    }
}
