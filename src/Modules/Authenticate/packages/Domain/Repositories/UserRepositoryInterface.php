<?php

namespace Modules\Authenticate\Packages\Domain\Repositories;

use Modules\Authenticate\Packages\Domain\Models\User;
use Modules\Authenticate\Packages\Domain\Models\Email;

interface UserRepositoryInterface
{
    public function findByEmail(Email $email): ?User;
    public function save(User $user): User;
}
