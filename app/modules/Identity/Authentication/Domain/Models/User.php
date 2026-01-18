<?php

declare(strict_types=1);

namespace App\Modules\Identity\Authentication\Domain\Models;

final class User
{
    public function __construct(
        public readonly string $id,
        public readonly Email $email,
        public readonly Password $password
    ) {
    }
}
