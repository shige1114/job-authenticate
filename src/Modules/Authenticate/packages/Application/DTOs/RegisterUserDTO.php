<?php

namespace Modules\Authenticate\Packages\Application\DTOs;

class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}
}
