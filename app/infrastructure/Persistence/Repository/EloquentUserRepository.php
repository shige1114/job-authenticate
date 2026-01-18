<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Modules\Identity\Authentication\Domain\Models\Email;
use App\Modules\Identity\Authentication\Domain\Models\Password;
use App\Modules\Identity\Authentication\Domain\Models\User as DomainUser;
use App\Modules\Identity\Authentication\Domain\Repositories\UserRepository;
use App\Infrastructure\Persistence\Eloquent\User as EloquentUser;

class EloquentUserRepository implements UserRepository
{
    public function findByEmail(string $email): ?EloquentUser
    {
        $eloquentUser = EloquentUser::where('email', $email)->first();

        return $eloquentUser;
    public function save(EloquentUser $user): void
    {
        $user->save();
    }
}
