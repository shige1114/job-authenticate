<?php

use Illuminate\Support\Facades\Route;
use Modules\Authenticate\Http\Controllers\AuthenticateController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('authenticates', AuthenticateController::class)->names('authenticate');
});
