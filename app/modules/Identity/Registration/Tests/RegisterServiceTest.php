<?php

declare(strict_types=1);

namespace App\Modules\Identity\Registration\Tests;

use App\Modules\Identity\Authentication\Domain\Repositories\UserRepository;
use App\Modules\Identity\Registration\Application\RegisterService;
use App\Modules\Identity\Registration\Domain\Exceptions\EmailAlreadyExistsException;
use App\Modules\Identity\Registration\Domain\Exceptions\PasswordMismatchException;
use App\Infrastructure\Persistence\Eloquent\User as EloquentUser;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class RegisterServiceTest extends TestCase
{
    private UserRepository|Mockery\MockInterface $userRepository;
    private RegisterService $registerService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->registerService = new RegisterService($this->userRepository);
    }

    public function test_successful_user_registration(): void
    {
        // Given
        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123';
        $passwordConfirmation = 'password123';

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->with($email)
            ->andReturn(null); // Email is unique

        $this->userRepository
            ->shouldReceive('save')
            ->once()
            ->andReturnUsing(function (EloquentUser $user) use ($name, $email, $password) {
                // Simulate saving and returning the user with an ID
                $user->id = 1;
                $user->name = $name;
                $user->email = $email;
                $user->password = Hash::make($password);
                return $user;
            });

        // When
        $registeredUser = $this->registerService->register($name, $email, $password, $passwordConfirmation);

        // Then
        $this->assertInstanceOf(EloquentUser::class, $registeredUser);
        $this->assertEquals($name, $registeredUser->name);
        $this->assertEquals($email, $registeredUser->email);
        $this->assertTrue(Hash::check($password, $registeredUser->password));
    }

    public function test_registration_fails_if_email_already_exists(): void
    {
        // Expect
        $this->expectException(EmailAlreadyExistsException::class);

        // Given
        $name = 'Existing User';
        $email = 'existing@example.com';
        $password = 'password123';
        $passwordConfirmation = 'password123';

        $existingUser = new EloquentUser();
        $existingUser->id = 1;
        $existingUser->email = $email;

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->with($email)
            ->andReturn($existingUser); // Email already exists

        $this->userRepository->shouldNotReceive('save'); // Save should not be called

        // When
        $this->registerService->register($name, $email, $password, $passwordConfirmation);
    }

    public function test_registration_fails_if_passwords_do_not_match(): void
    {
        // Expect
        $this->expectException(PasswordMismatchException::class);

        // Given
        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password123';
        $passwordConfirmation = 'different-password'; // Mismatched password

        $this->userRepository->shouldNotReceive('findByEmail'); // No need to check email uniqueness
        $this->userRepository->shouldNotReceive('save'); // Save should not be called

        // When
        $this->registerService->register($name, $email, $password, $passwordConfirmation);
    }
}
