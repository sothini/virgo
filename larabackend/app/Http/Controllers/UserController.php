<?php

namespace App\Http\Controllers;

use App\Interfaces\IUserRepository;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    protected $repository;
    public function __construct(IUserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get authenticated user profile
     *
     * Retrieves the profile information of the currently authenticated user.
     */
    #[OA\Get(
        path: '/api/profile',
        summary: 'Get user profile',
        description: 'Retrieves the profile information of the currently authenticated user',
        operationId: 'getUserProfile',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User profile retrieved successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
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
            new OA\Response(
                response: 404,
                description: 'User not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'User not found.'),
                    ]
                )
            ),
        ]
    )]
    public function getProfile()
    {
        return $this->repository->getProfile();
    }
}
