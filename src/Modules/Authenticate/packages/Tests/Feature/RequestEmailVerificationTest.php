<?php

namespace Modules\Authenticate\Packages\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Authenticate\Packages\Application\UseCases\RequestEmailVerificationUseCase;
use Tests\TestCase;

class RequestEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requests_email_verification_successfully(): void
    {
        // Mock the use case
        $useCaseMock = $this->createMock(RequestEmailVerificationUseCase::class);
        $this->app->instance(RequestEmailVerificationUseCase::class, $useCaseMock);

        $email = 'test@example.com';

        // Expect the execute method to be called once with the specified email
        $useCaseMock->expects($this->once())
            ->method('execute')
            ->with($email);

        // Act
        $response = $this->postJson('/api/v1/request-email-verification', ['email' => $email]);

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['message' => 'A verification email has been sent.']);
    }

    public function test_it_returns_validation_error_if_email_is_missing(): void
    {
        // Act
        $response = $this->postJson('/api/v1/request-email-verification', []);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_it_returns_validation_error_if_email_is_invalid(): void
    {
        // Act
        $response = $this->postJson('/api/v1/request-email-verification', ['email' => 'not-an-email']);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_it_handles_use_case_exceptions_gracefully(): void
    {
        // Mock the use case to throw an exception
        $useCaseMock = $this->createMock(RequestEmailVerificationUseCase::class);
        $this->app->instance(RequestEmailVerificationUseCase::class, $useCaseMock);

        $email = 'test@example.com';

        $useCaseMock->expects($this->once())
            ->method('execute')
            ->with($email)
            ->will($this->throwException(new \Exception('Something went wrong')));

        // Act
        $response = $this->postJson('/api/v1/request-email-verification', ['email' => $email]);

        // Assert
        $response->assertStatus(500);
        $response->assertJson(['message' => 'An unexpected error has occurred.']);
    }
}
