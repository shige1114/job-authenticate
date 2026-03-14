<?php

declare(strict_types=1);

namespace Modules\Authenticate\Packages\Domain\Models\ValueObjects;

use InvalidArgumentException;

class Password
{
    private string $hash;

    public function __construct(string $hash)
    {
        if (empty($hash)) {
            throw new InvalidArgumentException("Password hash cannot be empty.");
        }
        $this->hash = $hash;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function __toString(): string
    {
        return $this->hash;
    }
}
