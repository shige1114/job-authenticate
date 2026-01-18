<?php

declare(strict_types=1);

namespace App\Modules\Identity\Authentication\Presentation;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Authentication\Application\AuthenticationService;
use App\Modules\Identity\Authentication\Domain\Exceptions\AuthenticationException;
use App\Modules\Identity\Authentication\Domain\Exceptions\UserNotFoundException;
use Illuminate\Http\JsonResponse; // Changed return type
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuthenticationService $authenticationService
    ) {}

    public function showLoginForm()
    {
        return view('auth.login'); // Assuming a Blade view for login form
    }

    public function login(Request $request): JsonResponse // Changed return type
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        // The AuthenticationService is still used for the business logic check,
        // but the actual login is handled by JWT guard
        try {
            // This call ensures the business logic of finding user and checking password is done
            // It also throws exceptions if user not found or password incorrect
            $this->authenticationService->authenticate($credentials['email'], $credentials['password']);
        } catch (UserNotFoundException | AuthenticationException $e) {
            // This should ideally not happen if auth()->attempt already failed
            // but is a safeguard if the AuthenticationService has more complex checks
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }


        return $this->respondWithToken($token);
    }

    public function logout(Request $request): JsonResponse // Changed return type
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
