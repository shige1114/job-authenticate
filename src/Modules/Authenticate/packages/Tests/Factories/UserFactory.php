<?php

namespace Modules\Authenticate\Packages\Tests\Factories;

use Modules\Authenticate\Packages\Domain\Models\Entities\User;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Name;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Password;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\UserID;

class UserFactory
{
    public static function new(
        ?UserID $userId = null,
        ?Email $email = null,
        ?Password $password = null,
    ): User {
        return new User(
            $userId ?? new UserID(1),
            $email ?? new Email('test@example.com'),
            $password ?? new Password('password'),
        );
    }
}
