<?php

namespace Modules\Authenticate\Packages\Domain\Models\Entities;

use DateTimeImmutable;
use InvalidArgumentException;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Password;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\EmailVerifiedAt;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Name;

class User
{
    private string $userId;
    private Email $email;
    private Password $password; // Nullable password
    private EmailVerifiedAt $emailVerifiedAt; // Nullable
    private Name $name; // Assuming default name for provisional user, or it's set later

    public function __construct(
        string $userId,
        Email $email,
        Password $password, // Nullable
        EmailVerifiedAt $emailVerifiedAt, // Nullable
        Name $name // Default name for now
    ) {
        if (empty($userId)) {
            throw new InvalidArgumentException("User ID cannot be empty.");
        }
        $this->userId = $userId;
        $this->email = $email;
        $this->password = $password;
        $this->emailVerifiedAt = $emailVerifiedAt;
        $this->name = $name;
    }


    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): ?Password
    {
        return $this->password;
    }

    public function getEmailVerifiedAt(): ?EmailVerifiedAt
    {
        return $this->emailVerifiedAt;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function markEmailAsVerified(EmailVerifiedAt $emailVerifiedAt): void
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
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
