<?php

namespace App\Http\Controllers;


use App\Http\Requests\LoginRequest;
use App\Interfaces\IAuthRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(private readonly IAuthRepository $authRepository)
    {
    }

    /**
     * Login user and create a Sanctum token
     *
     * Authenticates a user with email and password, and returns a Sanctum access token.
     */
    #[OA\Post(
        path: '/api/login',
        summary: 'Login user',
        description: 'Authenticates a user with email and password, and returns a Sanctum access token',
        operationId: 'login',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string', example: '1|abcdefghijklmnopqrstuvwxyz1234567890'),
                        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'email', type: 'array', items: new OA\Items(type: 'string')),
                                new OA\Property(property: 'password', type: 'array', items: new OA\Items(type: 'string')),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid credentials',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The provided credentials are incorrect.'),
                    ]
                )
            ),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $loginData = $this->authRepository->login($request);

        return response()->json($loginData, 200);
    }

    /**
     * Logout user and revoke current token
     *
     * Revokes the current access token for the authenticated user.
     */
    #[OA\Post(
        path: '/api/logout',
        summary: 'Logout user',
        description: 'Revokes the current access token for the authenticated user',
        operationId: 'logout',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully.'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $this->authRepository->logout($request);

        return response()->json([
            'message' => 'Logged out successfully.',
        ], 200);
    }

    
}

