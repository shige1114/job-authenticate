<?php

use Illuminate\Support\Facades\Route;
use Modules\Authenticate\Http\Controllers\AuthenticateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('email')->group(function () {
    Route::get('/verify/request', function () {
        return view('authenticate::request-email-verification');
    })->name('authenticate.email.verify.request');

    Route::get('/verify', function () {
        return view('authenticate::verify-email');
    })->name('authenticate.email.verify');
});

