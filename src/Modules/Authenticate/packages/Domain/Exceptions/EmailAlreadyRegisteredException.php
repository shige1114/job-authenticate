<?php

namespace Modules\Authenticate\Packages\Domain\Exceptions;

use Exception;
use Throwable; // Import Throwable

class EmailAlreadyRegisteredException extends Exception
{
    public function __construct(string $email, int $code = 0, ?Throwable $previous = null)
    {
        $message = "The email address '{$email}' is already registered.";
        parent::__construct($message, $code, $previous);
    }
}
