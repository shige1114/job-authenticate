<?php

namespace Modules\Authenticate\Packages\Domain\Repositories;

use Modules\Authenticate\Packages\Domain\Models\Entities\User;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;

interface UserRepository
{
    public function save(User $user): void;
    public function findByEmail(Email $email): ?User;
    public function findById(string $userId): ?User;
}
