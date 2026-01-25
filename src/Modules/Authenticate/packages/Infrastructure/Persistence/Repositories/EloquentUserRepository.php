<?php

namespace Modules\Authenticate\Packages\Infrastructure\Persistence\Repositories;

use Modules\Authenticate\Packages\Domain\Models\Entities\User as DomainUser;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Password;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\EmailVerifiedAt;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Name;
use Modules\Authenticate\Packages\Domain\Repositories\UserRepository;
use App\Models\User as EloquentUser; // Alias for the Eloquent User model
use DateTimeImmutable;

class EloquentUserRepository implements UserRepository
{
    private EloquentUser $eloquentUser;

    public function __construct(EloquentUser $eloquentUser)
    {
        $this->eloquentUser = $eloquentUser;
    }

    public function save(DomainUser $domainUser): void
    {
        $eloquentUser = $this->eloquentUser->newQuery()->find($domainUser->getUserId());

        if (!$eloquentUser) {
            $eloquentUser = $this->eloquentUser->newInstance();
            $eloquentUser->id = $domainUser->getUserId(); // Assume 'id' column maps to userId
        }

        $eloquentUser->email = $domainUser->getEmail()->getAddress();
        $eloquentUser->email_verified_at = $domainUser->getEmailVerifiedAt() ? $domainUser->getEmailVerifiedAt()->getVerifiedAt() : null;
        $eloquentUser->name = $domainUser->getName()->getFullName(); // Assuming a 'name' field in EloquentUser

        if ($domainUser->getPassword()) {
            // Eloquent User model casts password to 'hashed', so we pass the plain string
            $eloquentUser->password = $domainUser->getPassword()->getPlainPassword(); // Assuming Password value object can return plain for hashing
        } else {
            $eloquentUser->password = null;
        }

        $eloquentUser->save();
    }

    public function findByEmail(Email $email): ?DomainUser
    {
        $eloquentUser = $this->eloquentUser->newQuery()->where('email', $email->getAddress())->first();

        if (!$eloquentUser) {
            return null;
        }

        return $this->mapEloquentToDomain($eloquentUser);
    }

    public function findById(string $userId): ?DomainUser
    {
        $eloquentUser = $this->eloquentUser->newQuery()->find($userId);

        if (!$eloquentUser) {
            return null;
        }

        return $this->mapEloquentToDomain($eloquentUser);
    }

    private function mapEloquentToDomain(EloquentUser $eloquentUser): DomainUser
    {
        $password = $eloquentUser->password ? new Password($eloquentUser->password) : null;
        $emailVerifiedAt = $eloquentUser->email_verified_at ? new EmailVerifiedAt(new DateTimeImmutable($eloquentUser->email_verified_at)) : null;

        // Assuming Name value object can be created from a single 'name' string for now
        $name = new Name($eloquentUser->name ?? 'Provisional', ''); // Provide default if name is null

        return new DomainUser(
            $eloquentUser->id,
            new Email($eloquentUser->email),
            $password,
            $emailVerifiedAt,
            $name
        );
    }
}
