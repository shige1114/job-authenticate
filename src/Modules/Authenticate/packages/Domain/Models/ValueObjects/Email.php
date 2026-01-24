<?php

namespace Modules\Authenticate\Packages\Domain\Models\ValueObjects;

use InvalidArgumentException;

class Email
{
    private string $address;

    public function __construct(string $address)
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: " . $address);
        }
        $this->address = $address;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function equals(Email $other): bool
    {
        return $this->address === $other->address;
    }

    public function __toString(): string
    {
        return $this->address;
    }
}
