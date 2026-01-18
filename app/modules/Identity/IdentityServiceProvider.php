<?php

declare(strict_types=1);

namespace App\Modules\Identity;

use App\Modules\Identity\Authentication\Domain\Repositories\UserRepository;
use App\Infrastructure\Persistence\Repository\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class IdentityServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load module API routes
        Route::prefix('api')
            ->middleware('api')
            ->namespace('App\Modules\Identity\Authentication\Presentation')
            ->group(__DIR__ . '/Authentication/routes/api.php');

        // Optional: Load web routes if showLoginForm is still needed,
        // but for pure JWT API, this might be removed.
        // For now, let's keep it commented or remove it if not explicitly requested.
        // For this task, focusing on JWT API.
        // Route::middleware('web')
        //     ->namespace('App\Modules\Identity\Authentication\Presentation')
        //     ->group(__DIR__ . '/Authentication/routes/web.php');
    }
}
