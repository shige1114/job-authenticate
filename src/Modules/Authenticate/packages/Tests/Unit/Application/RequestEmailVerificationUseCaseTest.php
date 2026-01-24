<?php

namespace Modules\Authenticate\Packages\Tests\Unit\Application;

use Tests\TestCase;
use Modules\Authenticate\Packages\Application\UseCases\RequestEmailVerificationUseCase;
use Modules\Authenticate\Packages\Domain\Models\ValueObjects\Email;
use Modules\Authenticate\Packages\Domain\Models\Entities\PendingEmailVerification;
use Modules\Authenticate\Packages\Domain\Repositories\PendingEmailVerificationRepository;
use Modules\Authenticate\Packages\Domain\Repositories\EmailVerificationMailer; // This will be an interface
use Modules\Authenticate\Packages\Domain\Exceptions\EmailAlreadyRegisteredException; // Need to create this exception
use Mockery;

class RequestEmailVerificationUseCaseTest extends TestCase
{
    private $pendingEmailVerificationRepository;
    private $emailVerificationMailer;
    private RequestEmailVerificationUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pendingEmailVerificationRepository = Mockery::mock(PendingEmailVerificationRepository::class);
        $this->emailVerificationMailer = Mockery::mock(EmailVerificationMailer::class);

        $this->useCase = new RequestEmailVerificationUseCase(
            $this->pendingEmailVerificationRepository,
            $this->emailVerificationMailer
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function test_it_requests_email_verification_successfully()
    {
        $emailString = 'test@example.com';
        $email = new Email($emailString);

        $this->pendingEmailVerificationRepository
            ->shouldReceive('save')
            ->once()
            ->andReturnUsing(function (PendingEmailVerification $pendingEmailVerification) use ($email) {
                $this->assertEquals($email->getAddress(), $pendingEmailVerification->getEmail()->getAddress());
                $this->assertNotEmpty($pendingEmailVerification->getToken());
                $this->assertFalse($pendingEmailVerification->isExpired());
            });

        $this->emailVerificationMailer
            ->shouldReceive('send')
            ->once()
            ->andReturn(true);

        $this->useCase->execute($emailString);
    }

    public function test_it_throws_exception_for_invalid_email_format()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address: invalid-email');

        $this->useCase->execute('invalid-email');
    }

    public function test_it_throws_exception_if_email_is_already_registered()
    {
        // This test case requires a UserRepository concept, which is not yet in our domain model or use case.
        // For now, I'll comment it out or adapt the use case to have a simpler check.
        // Let's assume for now the use case does NOT check if a User *exists*, but only if a *pending* verification exists.
        // If a User exists, this use case might need a UserRepository.
        // Given the current diagram focuses only on PendingEmailVerification, I'll skip the "User already registered" check for now,
        // but keep it in mind for future refinement or when a UserRepository is introduced.

        $this->markTestSkipped('This test requires a UserRepository to check for existing users, which is not yet integrated.');
    }

    public function test_it_does_not_send_email_if_save_fails()
    {
        $emailString = 'test@example.com';
        $email = new Email($emailString);

        $this->pendingEmailVerificationRepository
            ->shouldReceive('save')
            ->once()
            ->andThrow(new \Exception("Database error"));

        $this->emailVerificationMailer->shouldReceive('send')->never();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Database error");

        $this->useCase->execute($emailString);
    }
}
