<?php

use Illuminate\Support\Facades\Route;
use Modules\Authenticate\Packages\Presentation\Controllers\RequestEmailVerificationController;
use Modules\Authenticate\Packages\Presentation\Controllers\VerifyEmailController;

Route::prefix('v1')->group(function () {
    Route::post('/request-email-verification', RequestEmailVerificationController::class);
});
Route::prefix('v1')->group(function () {
    Route::post('/email-verification', VerifyEmailController::class);
});
