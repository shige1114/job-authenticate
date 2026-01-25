<?php

namespace Modules\Authenticate\Packages\Domain\Repositories;

use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;

interface EmailVerificationMailer
{
    public function send(Email $email, string $code): bool;
}
