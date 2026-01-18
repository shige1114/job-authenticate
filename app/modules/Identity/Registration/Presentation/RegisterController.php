<?php

declare(strict_types=1);

namespace App\Modules\Identity\Registration\Presentation;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Registration\Application\RegisterService;
use App\Modules\Identity\Registration\Domain\Exceptions\EmailAlreadyExistsException;
use App\Modules\Identity\Registration\Domain\Exceptions\PasswordMismatchException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function __construct(
        private readonly RegisterService $registerService
    ) {
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // 'unique:users' is for Eloquent User
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $user = $this->registerService->register(
                $request->input('name'),
                $request->input('email'),
                $request->input('password'),
                $request->input('password_confirmation')
            );
        } catch (EmailAlreadyExistsException $e) {
            throw ValidationException::withMessages([
                'email' => ['このメールアドレスは既に使用されています。'],
            ]);
        } catch (PasswordMismatchException $e) {
            throw ValidationException::withMessages([
                'password' => ['パスワードが一致しません。'],
            ]);
        }

        // Upon successful registration, you might want to automatically log in the user
        // and return a JWT token, similar to the LoginController.
        // For now, just return a success response.
        return response()->json([
            'message' => 'User registered successfully!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 201);
    }
}
