<?php

namespace App\Http\Controllers;


class LogoutAdminController extends Controller
{
    public function __invoke()
    {
        auth()->guard('admin')->logout();
        session()->flush();

        return redirect(route('admin.login'));
    }
}
