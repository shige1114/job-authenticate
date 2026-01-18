<?php

declare(strict_types=1);

namespace App\Modules\Identity\Authentication\Domain\Models;

use InvalidArgumentException;

final readonly class Password
{
    public function __construct(public string $hashedValue)
    {
        if (strlen($this->hashedValue) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters long.');
        }
    }
}
