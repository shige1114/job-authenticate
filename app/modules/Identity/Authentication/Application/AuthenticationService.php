<?php

declare(strict_types=1);

namespace App\Modules\Identity\Authentication\Application;

use App\Modules\Identity\Authentication\Domain\Exceptions\AuthenticationException;
use App\Modules\Identity\Authentication\Domain\Exceptions\UserNotFoundException;
use App\Models\User;
use App\Modules\Identity\Authentication\Domain\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function authenticate(string $email, string $password): \App\Infrastructure\Persistence\Eloquent\User // Changed return type
    {
        $user = $this->userRepository->findByEmail($email); // Now returns EloquentUser

        if ($user === null) {
            throw new UserNotFoundException('User with this email not found.');
        }

        if (!Hash::check($password, $user->password)) { // Changed password check
            throw new AuthenticationException('Invalid credentials.');
        }

        return $user;
    }
}
