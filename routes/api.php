<?php

use App\Http\Controllers\Api\HasilPspkController;
use App\Http\Controllers\Api\HasilTesPotensiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware('client')->group(function () {
    Route::get('/hasil-pspk', [HasilPspkController::class, 'index']);
    Route::get('/hasil-tes-potensi', [HasilTesPotensiController::class, 'index']);
});
