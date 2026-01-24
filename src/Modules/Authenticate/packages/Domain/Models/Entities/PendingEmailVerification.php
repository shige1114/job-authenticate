<?php

namespace Modules\Authenticate\Packages\Domain\Models\Entities;

use DateTimeImmutable;
use InvalidArgumentException;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;

class PendingEmailVerification
{
    private string $token;
    private Email $email;
    private DateTimeImmutable $expiresAt;

    public function __construct(string $token, Email $email, DateTimeImmutable $expiresAt)
    {
        if (empty($token)) {
            throw new InvalidArgumentException("Token cannot be empty.");
        }
        $this->token = $token;
        $this->email = $email;
        $this->expiresAt = $expiresAt;
    }

    public static function create(Email $email, int $expirationMinutes = 60): self
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = (new DateTimeImmutable())->modify("+$expirationMinutes minutes");
        return new self($token, $email, $expiresAt);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function isValid(): bool
    {
        return $this->expiresAt > new DateTimeImmutable();
    }

    public function isExpired(): bool
    {
        return !$this->isValid();
    }
}
