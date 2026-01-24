<?php

namespace Modules\Authenticate\Packages\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class PendingEmailVerificationModel extends Model
{
    protected $table = 'pending_email_verifications';
    protected $primaryKey = 'token'; // Using token as primary key
    public $incrementing = false; // Primary key is not auto-incrementing
    protected $keyType = 'string'; // Primary key type is string

    protected $fillable = [
        'token',
        'email',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
