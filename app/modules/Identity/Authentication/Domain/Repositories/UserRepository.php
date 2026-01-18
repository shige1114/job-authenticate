<?php

declare(strict_types=1);

namespace App\Modules\Identity\Authentication\Domain\Repositories;

use App\Infrastructure\Persistence\Eloquent\User as EloquentUser;

interface UserRepository
{
    public function findByEmail(string $email): ?EloquentUser;
    public function save(\App\Infrastructure\Persistence\Eloquent\User $user): void;
}
