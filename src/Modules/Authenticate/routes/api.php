<?php

use Illuminate\Support\Facades\Route;
use Modules\Authenticate\Http\Controllers\AuthenticateController;
use Modules\Authenticate\Packages\Presentation\Controllers\RequestEmailVerificationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('authenticates', AuthenticateController::class)->names('authenticate');
});

Route::prefix('v1')->group(function () {
    Route::post('/request-email-verification', RequestEmailVerificationController::class);
});
