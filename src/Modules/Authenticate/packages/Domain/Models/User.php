<?php

namespace Modules\Authenticate\Packages\Domain\Models;

use Modules\Authenticate\Packages\Domain\Models\Email;
use Modules\Authenticate\Packages\Domain\Models\Password;
use Modules\Authenticate\Packages\Domain\Models\Name;
use Modules\Authenticate\Packages\Domain\Models\EmailVerifiedAt;
use Modules\Authenticate\Packages\Domain\Models\Token;
use Modules\Authenticate\Packages\Domain\Models\TokenType;
use Ramsey\Uuid\Uuid;
use DateTimeImmutable;

class User
{
    private string $id;
    private Email $email;
    private Password $password;
    private Name $name;
    private ?EmailVerifiedAt $emailVerifiedAt;

    private ?Email $pendingNewEmail = null;
}
