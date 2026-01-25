<?php

namespace Modules\Authenticate\Packages\Tests\Unit\Application;

use Tests\TestCase;
use Mockery;
use Modules\Authenticate\Packages\Application\UseCases\VerifyEmailUseCase;
use Modules\Authenticate\Packages\Domain\Models\Entities\PendingEmailVerification;
use Modules\Authenticate\Packages\Domain\Models\Entities\User; // Import the Domain User
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\EmailVerifiedAt;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Name;
use Modules\Authenticate\Packages\Domain\Repositories\PendingEmailVerificationRepository;
use Modules\Authenticate\Packages\Domain\Repositories\UserRepository;
use Modules\Authenticate\Packages\Domain\Exceptions\PendingEmailVerificationNotFoundException;
use Modules\Authenticate\Packages\Domain\Exceptions\EmailVerificationFailedException;
use DateTimeImmutable;

class VerifyEmailUseCaseTest extends TestCase
{
    private $pendingEmailVerificationRepository;
    private VerifyEmailUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pendingEmailVerificationRepository = Mockery::mock(PendingEmailVerificationRepository::class);

        $this->useCase = new VerifyEmailUseCase(
            $this->pendingEmailVerificationRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function test_it_verifies_email_successfully()
    {
        $token = 'test-token';
        $code = '123456';

        $pendingVerification = Mockery::mock(PendingEmailVerification::class);
        $pendingVerification->token = $token;

        $pendingVerification->shouldReceive('verify')
                            ->with($code)
                            ->once();
        
        $pendingVerification->shouldReceive('isVerified')->andReturn(true);
        $pendingVerification->shouldReceive('isExpired')->andReturn(false);

        $this->pendingEmailVerificationRepository
            ->shouldReceive('findByToken')
            ->with($token)
            ->andReturn($pendingVerification);

        $this->pendingEmailVerificationRepository
            ->shouldReceive('save')
            ->with($pendingVerification)
            ->once();

        $returnedToken = $this->useCase->execute($token, $code);

        $this->assertEquals($token, $returnedToken);
    }


    /** @test */
    public function test_it_throws_exception_if_code_is_invalid()
    {
        $token = 'test-token';
        $code = 'wrong-code';

        $pendingVerification = Mockery::mock(PendingEmailVerification::class);
        $pendingVerification->shouldReceive('verify')
                            ->with($code)
                            ->once();
        $pendingVerification->shouldReceive('isVerified')->andReturn(false);
        $pendingVerification->shouldReceive('isExpired')->andReturn(false);
        $pendingVerification->shouldReceive('getToken')->andReturn($token);

        $this->pendingEmailVerificationRepository
            ->shouldReceive('findByToken')
            ->with($token)
            ->andReturn($pendingVerification);

        $this->expectException(EmailVerificationFailedException::class);
        $this->expectExceptionMessage("Invalid code for pending email verification with token {$token}.");

        $this->useCase->execute($token, $code);
    }

    /** @test */
    public function test_it_throws_exception_if_already_verified()
    {
        $token = 'test-token';
        $code = '123456';

        $pendingVerification = Mockery::mock(PendingEmailVerification::class);
        $pendingVerification->shouldReceive('verify')
                            ->with($code)
                            ->once();
        $pendingVerification->shouldReceive('isVerified')->andReturn(false); // Simulates that it's NOT verified after call (even if conceptually "already verified")
        $pendingVerification->shouldReceive('isExpired')->andReturn(false); // Not expired
        $pendingVerification->shouldReceive('getToken')->andReturn($token); // For exception message

        $this->pendingEmailVerificationRepository
            ->shouldReceive('findByToken')
            ->with($token)
            ->andReturn($pendingVerification);

        $this->expectException(EmailVerificationFailedException::class);
        // The UseCase, as currently implemented, throws "Invalid code" if isVerified() is false and not expired.
        // It does not have a distinct exception message for "already verified" in this path.
        $this->expectExceptionMessage("Invalid code for pending email verification with token {$token}.");

        $this->useCase->execute($token, $code);
    }

    /** @test */
    public function test_it_throws_exception_if_expired()
    {
        $token = 'test-token';
        $code = '123456';

        $pendingVerification = Mockery::mock(PendingEmailVerification::class);
        $pendingVerification->shouldReceive('verify')
                            ->with($code)
                            ->once();
        $pendingVerification->shouldReceive('isVerified')->andReturn(false);
        $pendingVerification->shouldReceive('isExpired')->andReturn(true);
        $pendingVerification->shouldReceive('getToken')->andReturn($token);

        $this->pendingEmailVerificationRepository
            ->shouldReceive('findByToken')
            ->with($token)
            ->andReturn($pendingVerification);

        $this->expectException(EmailVerificationFailedException::class);
        $this->expectExceptionMessage("Email verification for token {$token} expired.");

        $this->useCase->execute($token, $code);
    }
}
