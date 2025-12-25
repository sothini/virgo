<?php

namespace App\Repositories;

use App\Http\Requests\LoginRequest;
use App\Interfaces\IAuthRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthRepository implements IAuthRepository
{
    public function login(LoginRequest $request): array
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    public function logout(Request $request): void
    {
        $user = $request->user();
      
        if ($user) {
            // Delete all tokens for the user to ensure complete logout
            $user->tokens()->delete();
        }
        
        // Invalidate the session (for SPA/session-based authentication)
        Auth::logout();
        
        // Regenerate session ID to prevent session fixation attacks
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}

