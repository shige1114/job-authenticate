<?php

use App\Modules\Identity\Authentication\Presentation\LoginController;
use App\Modules\Identity\Registration\Presentation\RegisterController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('logout', [LoginController::class, 'logout']);
    Route::post('refresh', function () {
        return response()->json(['access_token' => auth('api')->refresh()]);
    });
    Route::post('me', function () {
        return response()->json(auth('api')->user());
    });

    // Protected route for dashboard
    Route::group(['middleware' => 'auth:api'], function ($router) {
        Route::get('dashboard', [DashboardController::class, 'index']);
    });
});
