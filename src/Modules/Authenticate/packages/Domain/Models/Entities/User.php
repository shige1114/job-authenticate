<?php

declare(strict_types=1);

namespace Modules\Authenticate\Packages\Domain\Models\Entities;

use InvalidArgumentException;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Password;

class User
{
    private string $userId;
    private Email $email;
    private Password $password; // Nullable password

    public function __construct(
        string $userId,
        Email $email,
        Password $password,
    ) {
        if (empty($userId)) {
            throw new InvalidArgumentException("User ID cannot be empty.");
        }
        $this->userId = $userId;
        $this->email = $email;
        $this->password = $password;
    }


    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }


    public function setPassword(Password $password): void
    {
        $this->password = $password;
    }

    public function hasPassword(): bool
    {
        return $this->password !== null;
    }
}
