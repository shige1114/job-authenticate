<?php

namespace Modules\Authenticate\Packages\Infrastructure\ExternalServices;

use Illuminate\Support\Facades\Mail; // 追加
use Illuminate\Support\Facades\Log;
use Modules\Authenticate\Packages\Domain\Models\Entities\PendingEmailVerification;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;
use Modules\Authenticate\Packages\Domain\Repositories\EmailVerificationMailer;
use Modules\Authenticate\Packages\Infrastructure\ExternalServices\VerificationEmail;



class SmtpEmailVerificationMailer implements EmailVerificationMailer
{
    public function send(Email $email, PendingEmailVerification $pendingEmailVerification): bool
    {
        try {
            // 1. ログも残しておく（デバッグ用）
            Log::info('Attempting to send SMTP email to: ' . $email->getAddress());

            // 2. 実際に送信！
            // VerificationEmail は artisan make:mail で作ったクラスです
            Mail::to($email->getAddress())->send(
                new VerificationEmail($pendingEmailVerification->getToken())
            );

            return true;
        } catch (\Exception $e) {
            Log::error('SMTP Send Failed: ' . $e->getMessage());
            return false;
        }
    }
}
