<?php

namespace Modules\Authenticate\Tests\Unit\Application;

use PHPUnit\Framework\TestCase;
use Modules\Authenticate\Packages\Application\UseCases\RegisterUserUseCase;
use Modules\Authenticate\Packages\Application\DTOs\RegisterUserDTO;
use Modules\Authenticate\Packages\Domain\Repositories\UserRepositoryInterface;
use Modules\Authenticate\Packages\Domain\Models\User;
use Modules\Authenticate\Packages\Domain\Models\Email;
use Modules\Authenticate\Packages\Domain\Models\Password;
use Modules\Authenticate\Packages\Domain\Models\Name;
use Modules\Authenticate\Packages\Domain\Exceptions\UserAlreadyExistsException;
use Modules\Authenticate\Packages\Domain\Exceptions\InvalidEmailException;
use Modules\Authenticate\Packages\Domain\Exceptions\WeakPasswordException;
use Mockery;
use Illuminate\Contracts\Events\Dispatcher; // Assuming Laravel's event dispatcher

class RegisterUserUseCaseTest extends TestCase
{
    protected $userRepository;
    protected $eventDispatcher;
    protected $registerUserUseCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->eventDispatcher = Mockery::mock(Dispatcher::class);
        $this->registerUserUseCase = new RegisterUserUseCase(
            $this->userRepository,
            $this->eventDispatcher
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_user_can_register_successfully(): void
    {
        $dto = new RegisterUserDTO('John Doe', 'john.doe@example.com', 'StrongPass123!');
        $email = new Email('john.doe@example.com');
        $password = new Password('StrongPass123!');
        $name = new Name('John', 'Doe');

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getEmail')->andReturn($email);
        $user->shouldReceive('getId')->andReturn('some-uuid');

        $this->userRepository->shouldReceive('findByEmail')
                             ->with($email)
                             ->andReturn(null)
                             ->once();

        $this->userRepository->shouldReceive('save')
                             ->with(Mockery::type(User::class))
                             ->andReturn($user)
                             ->once();

        $this->eventDispatcher->shouldReceive('dispatch')
                              ->once(); // Assuming an event is dispatched for email verification

        $registeredUser = $this->registerUserUseCase->handle($dto);

        $this->assertInstanceOf(User::class, $registeredUser);
        $this->assertEquals($email->address, $registeredUser->getEmail()->address);
    }

    public function test_registration_fails_with_invalid_email(): void
    {
        $this->expectException(InvalidEmailException::class);
        $dto = new RegisterUserDTO('John Doe', 'invalid-email', 'StrongPass123!');
        $this->registerUserUseCase->handle($dto);
    }

    public function test_registration_fails_with_weak_password(): void
    {
        $this->expectException(WeakPasswordException::class);
        $dto = new RegisterUserDTO('John Doe', 'john.doe@example.com', 'weak');
        $this->registerUserUseCase->handle($dto);
    }

    public function test_registration_fails_if_email_already_exists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $dto = new RegisterUserDTO('John Doe', 'john.doe@example.com', 'StrongPass123!');
        $email = new Email('john.doe@example.com');

        $this->userRepository->shouldReceive('findByEmail')
                             ->with($email)
                             ->andReturn(Mockery::mock(User::class)) // User already exists
                             ->once();

        $this->registerUserUseCase->handle($dto);
    }
}
