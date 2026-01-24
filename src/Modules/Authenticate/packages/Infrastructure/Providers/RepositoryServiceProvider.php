<?php

namespace Modules\Authenticate\Packages\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Authenticate\Packages\Domain\Repositories\EmailVerificationMailer;
use Modules\Authenticate\Packages\Domain\Repositories\PendingEmailVerificationRepository;
use Modules\Authenticate\Packages\Infrastructure\Persistence\Repositories\CachePendingEmailVerificationRepository;
use Modules\Authenticate\Packages\Infrastructure\ExternalServices\SmtpEmailVerificationMailer;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind(
            PendingEmailVerificationRepository::class,
            CachePendingEmailVerificationRepository::class
        );

        $this->app->bind(
            EmailVerificationMailer::class,
            SmtpEmailVerificationMailer::class
        );
    }
}
