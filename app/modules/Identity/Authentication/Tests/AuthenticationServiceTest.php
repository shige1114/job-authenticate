<?php

declare(strict_types=1);

namespace App\Modules\Identity\Authentication\Tests;

use App\Modules\Identity\Authentication\Application\AuthenticationService;
use App\Modules\Identity\Authentication\Domain\Exceptions\AuthenticationException;
use App\Modules\Identity\Authentication\Domain\Exceptions\UserNotFoundException;
use App\Infrastructure\Persistence\Eloquent\User as EloquentUser; // Use Eloquent User
use App\Modules\Identity\Authentication\Domain\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class AuthenticationServiceTest extends TestCase
{
    private UserRepository|Mockery\MockInterface $userRepository;
    private AuthenticationService $authenticationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->authenticationService = new AuthenticationService($this->userRepository);
    }

    public function test_successful_authentication(): void
    {
        // Given
        $email = 'test@example.com';
        $password = 'password123';
        
        $eloquentUser = new EloquentUser();
        $eloquentUser->id = 1;
        $eloquentUser->email = $email;
        $eloquentUser->password = Hash::make($password); // Eloquent user stores hashed password as string

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->with($email)
            ->andReturn($eloquentUser);

        // When
        $authenticatedUser = $this->authenticationService->authenticate($email, $password);

        // Then
        $this->assertSame($eloquentUser, $authenticatedUser);
    }

    public function test_authentication_fails_with_wrong_password(): void
    {
        // Expect
        $this->expectException(AuthenticationException::class);

        // Given
        $email = 'test@example.com';
        $password = 'password123';
        $wrongPassword = 'wrong-password';
        
        $eloquentUser = new EloquentUser();
        $eloquentUser->id = 1;
        $eloquentUser->email = $email;
        $eloquentUser->password = Hash::make($password);

        $this->userRepository
            ->shouldReceive('findByEmail')
            ->with($email)
            ->andReturn($eloquentUser);

        // When
        $this->authenticationService->authenticate($email, $wrongPassword);
    }

    public function test_authentication_fails_for_non_existent_user(): void
    {
        // Expect
        $this->expectException(UserNotFoundException::class);

        // Given
        $email = 'non-existent@example.com';
        $this->userRepository
            ->shouldReceive('findByEmail')
            ->with($email)
            ->andReturn(null);

        // When
        $this->authenticationService->authenticate($email, 'any-password');
    }
}
