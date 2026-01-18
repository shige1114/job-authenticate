<?php

declare(strict_types=1);

namespace App\Modules\Identity\Registration\Application;

use App\Modules\Identity\Authentication\Domain\Repositories\UserRepository;
use App\Modules\Identity\Registration\Domain\Exceptions\EmailAlreadyExistsException;
use App\Modules\Identity\Registration\Domain\Exceptions\PasswordMismatchException;
use App\Infrastructure\Persistence\Eloquent\User as EloquentUser;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function register(string $name, string $email, string $password, string $passwordConfirmation): EloquentUser
    {
        if ($password !== $passwordConfirmation) {
            throw new PasswordMismatchException('Passwords do not match.');
        }

        if ($this->userRepository->findByEmail($email) !== null) {
            throw new EmailAlreadyExistsException('User with this email already exists.');
        }

        $user = new EloquentUser();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);

        $this->userRepository->save($user);

        return $user;
    }
}
