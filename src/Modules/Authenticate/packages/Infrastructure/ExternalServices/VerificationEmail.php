<?php

namespace Modules\Authenticate\Packages\Infrastructure\ExternalServices;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $verificationUrl // テンプレートに渡す変数
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'メールアドレスの確認',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'authenticate::emails.verification',
        );
    }
}
