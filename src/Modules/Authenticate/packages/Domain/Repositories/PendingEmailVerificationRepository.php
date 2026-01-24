<?php

namespace Modules\Authenticate\Packages\Domain\Repositories;

use Modules\Authenticate\Packages\Domain\Models\Entities\PendingEmailVerification;

interface PendingEmailVerificationRepository
{
    public function save(PendingEmailVerification $pendingEmailVerification): void;
    public function findByToken(string $token): ?PendingEmailVerification;
    public function remove(PendingEmailVerification $pendingEmailVerification): void;
}
