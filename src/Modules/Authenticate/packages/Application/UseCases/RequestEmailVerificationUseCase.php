<?php

namespace Modules\Authenticate\Packages\Application\UseCases;

use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;
use Modules\Authenticate\Packages\Domain\Models\Entities\PendingEmailVerification;
use Modules\Authenticate\Packages\Domain\Repositories\PendingEmailVerificationRepository;
use Modules\Authenticate\Packages\Domain\Repositories\EmailVerificationMailer;

class RequestEmailVerificationUseCase
{
    private PendingEmailVerificationRepository $pendingEmailVerificationRepository;
    private EmailVerificationMailer $emailVerificationMailer;

    public function __construct(
        PendingEmailVerificationRepository $pendingEmailVerificationRepository,
        EmailVerificationMailer $emailVerificationMailer
    ) {
        $this->pendingEmailVerificationRepository = $pendingEmailVerificationRepository;
        $this->emailVerificationMailer = $emailVerificationMailer;
    }

    public function execute(string $emailString): void
    {
        $email = new Email($emailString); // Validates email format

        $pendingVerification = PendingEmailVerification::create($email);

        $this->pendingEmailVerificationRepository->save($pendingVerification);

        // Send verification email
        $this->emailVerificationMailer->send($email, $pendingVerification);
    }
}
