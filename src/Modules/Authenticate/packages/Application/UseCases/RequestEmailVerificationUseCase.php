<?php

namespace Modules\Authenticate\Packages\Application\UseCases;

use Exception;
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

    public function execute(string $emailString): string
    {
        $email = new Email($emailString); // Validates email format

        $sixDigitCode = $this->generateSixDigitCode(); // Generate 6-digit code

        $token = bin2hex(random_bytes(20));

        $pendingVerification = PendingEmailVerification::create($token, $email, $sixDigitCode); // Pass code

        $this->pendingEmailVerificationRepository->save($pendingVerification);

        // Send verification email
        if (!$this->emailVerificationMailer->send($email, $sixDigitCode)) { // Pass code directly
            throw new Exception("メール送信失敗");
        }

        return $token; // Return the code
    }

    private function generateSixDigitCode(): string
    {
        return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
}
