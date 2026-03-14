<?php

namespace Modules\Authenticate\Packages\Tests\Unit\Application;

use Tests\TestCase;
use Mockery;
use Modules\Authenticate\Packages\Application\UseCases\CreateUserUseCase;
use Modules\Authenticate\Packages\Domain\Models\Entities\User;
use Modules\Authenticate\Packages\Domain\Repositories\PendingEmailVerificationRepository;
use Modules\Authenticate\Packages\Domain\Repositories\UserRepository;
use Modules\Authenticate\Packages\Tests\Factories\PendingEmailVerificationFactory;
use Modules\Authenticate\Packages\Tests\Factories\UserFactory;

class CreateUserUsecaseTest extends TestCase
{

    private PendingEmailVerificationRepository $pendingEmailVerificationRepository;
    private UserRepository $userRepository;
    private CreateUserUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pendingEmailVerificationRepository = Mockery::mock(PendingEmailVerificationRepository::class);
        $this->userRepository = Mockery::mock(UserRepository::class);

        $this->useCase = new CreateUserUseCase(
            $this->userRepository,
            $this->pendingEmailVerificationRepository,
        );
    }
    public function testユーザの登録()
    {
        $pendingEmailVerification = PendingEmailVerificationFactory::new();

        $this->pendingEmailVerificationRepository
            ->shouldReceive('findByToken')
            ->once()
            ->andReturn($pendingEmailVerification);

        $this->userRepository
            ->shouldReceive('save')
            ->once()
            ->andReturn(true);


        $token = $this->useCase->execute($command);
        $this->assertNotNull($token);
    }
}
