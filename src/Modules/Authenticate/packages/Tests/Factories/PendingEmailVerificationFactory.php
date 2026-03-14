<?php

namespace Modules\Authenticate\Packages\Tests\Factories;

use Carbon\Carbon;
use Modules\Authenticate\Packages\Domain\Models\Entities\PendingEmailVerification;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Token;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\VerificationId;

class PendingEmailVerificationFactory
{
    public static function new(
        ?VerificationId $id = null,
        ?Email $email = null,
        ?Token $token = null,
        ?Carbon $expires_at = null
    ): PendingEmailVerification {
        return new PendingEmailVerification(
            $id ?? new VerificationId(1),
            $email ?? new Email('test@example.com'),
            $token ?? new Token('test_token'),
            $expires_at ?? Carbon::now()->addHour(),
        );
    }
}
