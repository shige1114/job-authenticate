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
    private string $code;
    private bool $isVerified;

    public function __construct(string $token, Email $email, DateTimeImmutable $expiresAt, string $code, bool $isVerified = false)
    {
        if (empty($token)) {
            throw new InvalidArgumentException("Token cannot be empty.");
        }
        if (empty($code)) {
            throw new InvalidArgumentException("Code cannot be empty.");
        }
        $this->token = $token;
        $this->email = $email;
        $this->expiresAt = $expiresAt;
        $this->code = $code;
        $this->isVerified = $isVerified;
    }

    public static function create(string $token, Email $email, string $code, int $expirationMinutes = 60): self
    {
        if (empty($code)) {
            throw new InvalidArgumentException("Code cannot be empty when creating PendingEmailVerification.");
        }
        $token = $token;
        $expiresAt = (new DateTimeImmutable())->modify("+$expirationMinutes minutes");
        return new self($token, $email, $expiresAt, $code, false); // Pass false for isVerified
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isVerified(): bool
    {
        return $this->isVerified && !$this->isExpired();
    }

    private function markAsVerified(bool $result): void
    {
        $this->isVerified = $result;
    }

    public function verify(string $code): void
    {
        $this->markAsVerified($this->code === $code && !$this->isExpired());
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new DateTimeImmutable();
    }
}
