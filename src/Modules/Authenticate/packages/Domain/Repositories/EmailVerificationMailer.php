<?php

namespace Modules\Authenticate\Packages\Domain\Repositories;

use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;
use Modules\Authenticate\Packages\Domain\Models\Entities\PendingEmailVerification;

interface EmailVerificationMailer
{
    public function send(Email $email, PendingEmailVerification $pendingEmailVerification): bool;
}
