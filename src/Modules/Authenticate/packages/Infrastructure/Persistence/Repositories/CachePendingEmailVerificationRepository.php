<?php

namespace Modules\Authenticate\Packages\Infrastructure\Persistence\Repositories;

use Illuminate\Support\Facades\Cache;
use Modules\Authenticate\Packages\Domain\Models\Entities\PendingEmailVerification;
use Modules\Authenticate\Packages\Domain\Repositories\PendingEmailVerificationRepository;

class CachePendingEmailVerificationRepository implements PendingEmailVerificationRepository
{
    private const CACHE_PREFIX = 'pending_email_verification:';

    public function save(PendingEmailVerification $pendingEmailVerification): void
    {
        $key = self::CACHE_PREFIX . $pendingEmailVerification->getToken();
        // The expiration of the pending verification object itself handles the lifetime,
        // so we can use that to determine the cache duration.
        $expiresAt = $pendingEmailVerification->getExpiresAt();
        Cache::put($key, $pendingEmailVerification, $expiresAt);
    }

    public function findByToken(string $token): ?PendingEmailVerification
    {
        $key = self::CACHE_PREFIX . $token;
        return Cache::get($key);
    }

    public function remove(PendingEmailVerification $pendingEmailVerification): void
    {
        $key = self::CACHE_PREFIX . $pendingEmailVerification->getToken();
        Cache::forget($key);
    }
}
