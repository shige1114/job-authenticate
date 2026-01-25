<?php

namespace Modules\Authenticate\Packages\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Authenticate\Packages\Application\UseCases\RequestEmailVerificationUseCase;
use Modules\Authenticate\Packages\Presentation\Requests\RequestEmailVerificationRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class RequestEmailVerificationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param RequestEmailVerificationRequest $request
     * @param RequestEmailVerificationUseCase $useCase
     * @return JsonResponse
     */
    public function __invoke(RequestEmailVerificationRequest $request, RequestEmailVerificationUseCase $useCase): JsonResponse
    {
        try {
            $token = $useCase->execute($request->validated()['email']);

            return response()->json(
                [
                    'message' => 'A verification email has been sent.',
                    'token' => $token,
                ],
                Response::HTTP_OK
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
