<?php

namespace Modules\Authenticate\Packages\Application\UseCases;

use Modules\Authenticate\Packages\Domain\Models\Entities\User;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\EmailVerifiedAt;
use Modules\Authenticate\Packages\Domain\Repositories\PendingEmailVerificationRepository;
use Modules\Authenticate\Packages\Domain\Repositories\UserRepository;
use Modules\Authenticate\Packages\Domain\Exceptions\PendingEmailVerificationNotFoundException;
use Modules\Authenticate\Packages\Domain\Exceptions\EmailVerificationFailedException;
use DateTimeImmutable; // Ensure DateTimeImmutable is imported

class VerifyEmailUseCase
{
    private PendingEmailVerificationRepository $pendingEmailVerificationRepository;

    public function __construct(
        PendingEmailVerificationRepository $pendingEmailVerificationRepository,
    ) {
        $this->pendingEmailVerificationRepository = $pendingEmailVerificationRepository;
    }

    public function execute(string $token, string $code): string
    {
        $pendingVerification = $this->pendingEmailVerificationRepository->findByToken($token);

        if (!$pendingVerification) {
            throw new PendingEmailVerificationNotFoundException("Pending email verification with token {$token} not found.");
        }

        $pendingVerification->verify($code);
        // Check if the code is valid (matches, not expired, not already verified)
        if (!$pendingVerification->isVerified()) {
            if ($pendingVerification->isExpired()) {
                throw new EmailVerificationFailedException("Email verification for token {$token} expired.");
            }
            throw new EmailVerificationFailedException("Invalid code for pending email verification with token {$token}.");
        }

        $this->pendingEmailVerificationRepository->save($pendingVerification);

        return $pendingVerification->token;
    }
}
