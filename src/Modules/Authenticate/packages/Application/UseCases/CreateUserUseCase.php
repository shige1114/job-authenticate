<?php

namespace Modules\Authenticate\Packages\Application\UseCases;

use DomainException;
use Illuminate\Support\Facades\Hash;
use Modules\Authenticate\Packages\Domain\Models\Entities\PendingEmailVerification;
use Modules\Authenticate\Packages\Domain\Models\Entities\User;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Password;
use Modules\Authenticate\Packages\Domain\Repositories\PendingEmailVerificationRepository;
use Modules\Authenticate\Packages\Domain\Repositories\UserRepository;
use Illuminate\Support\Str;

class CreateUserUseCase
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PendingEmailVerificationRepository $pendingEmailVerificationRepository
    ) {}

    public function execute(string $password, string $token): string
    {
        $pendingemailverification = $this->pendingEmailVerificationRepository->findByToken($token);
        if (!$pendingemailverification) {
            throw new PendingEmailVerificationNotFoundException("Pending email verification with token {$token} not found.");
        }
        if (!$pendingemailverification->isVerified()) {
            throw new DomainException("認証されていない");
        }
        $user = new User(
            (string) Str::uuid(),
            $pendingemailverification->getEmail(),
            new Password(Hash::make($password))
        );
        $this->userRepository->save($user);

        return $user->getUserId();
    }
}
