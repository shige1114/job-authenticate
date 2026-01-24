<?php

namespace Modules\Authenticate\Packages\Infrastructure\Persistence\Repositories;

use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;
use Modules\Authenticate\Packages\Domain\Models\Entities\PendingEmailVerification;
use Modules\Authenticate\Packages\Domain\Repositories\PendingEmailVerificationRepository;
use Modules\Authenticate\Packages\Infrastructure\Persistence\Eloquent\PendingEmailVerificationModel;
use DateTimeImmutable;

class EloquentPendingEmailVerificationRepository implements PendingEmailVerificationRepository
{
    public function save(PendingEmailVerification $pendingEmailVerification): void
    {
        PendingEmailVerificationModel::updateOrCreate(
            ['token' => $pendingEmailVerification->getToken()],
            [
                'email' => (string) $pendingEmailVerification->getEmail(),
                'expires_at' => $pendingEmailVerification->getExpiresAt(),
            ]
        );
    }

    public function findByToken(string $token): ?PendingEmailVerification
    {
        $model = PendingEmailVerificationModel::where('token', $token)->first();

        if (!$model) {
            return null;
        }

        return new PendingEmailVerification(
            $model->token,
            new Email($model->email),
            new DateTimeImmutable($model->expires_at)
        );
    }

    public function remove(PendingEmailVerification $pendingEmailVerification): void
    {
        PendingEmailVerificationModel::where('token', $pendingEmailVerification->getToken())->delete();
    }
}
