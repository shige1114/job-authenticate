<?php

namespace Modules\Authenticate\Packages\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Authenticate\Packages\Application\UseCases\VerifyEmailUseCase;
use Modules\Authenticate\Packages\Domain\Exceptions\EmailVerificationFailedException;
use Modules\Authenticate\Packages\Domain\Exceptions\PendingEmailVerificationNotFoundException;
use Modules\Authenticate\Packages\Presentation\Requests\VerifyEmailRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class VerifyEmailController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param VerifyEmailRequest $request
     * @param VerifyEmailUseCase $useCase
     * @return JsonResponse
     */
    public function __invoke(VerifyEmailRequest $request, VerifyEmailUseCase $useCase): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $token = $validatedData['token'];
            $code = $validatedData['code'];

            $token = $useCase->execute($token, $code);

            return response()->json(
                [
                    'message' => 'Email verified successfully.',
                    'token' => $token
                ],
                Response::HTTP_OK
            );
        } catch (PendingEmailVerificationNotFoundException $e) {
            Log::warning($e);
            return response()->json(
                ['message' => $e->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        } catch (EmailVerificationFailedException $e) {
            Log::warning($e);
            return response()->json(
                ['message' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (Throwable $e) {
            Log::error($e);
            return response()->json(
                ['message' => 'An unexpected error has occurred.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
