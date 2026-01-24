<?php

use Illuminate\Support\Facades\Route;
use Modules\Authenticate\Http\Controllers\AuthenticateController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('authenticates', AuthenticateController::class)->names('authenticate');
});
