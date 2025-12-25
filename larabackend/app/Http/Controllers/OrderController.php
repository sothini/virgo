<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Interfaces\IOrderRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Enums\Symbol;

class OrderController extends Controller
{
    protected $repository;

    public function __construct(IOrderRepository $repository)
    {
        $this->repository = $repository;
    }

    
    #[OA\Get(
        path: '/api/orders',
        summary: 'Get open orders',
        description: 'Returns all open orders for orderbook (buy & sell), optionally filtered by symbol',
        operationId: 'getOpenOrders',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'symbol',
                in: 'query',
                description: 'Filter orders by symbol (e.g., BTC)',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'BTC')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Open orders retrieved successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Order')
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
    public function index(Request $request): JsonResponse
    {
        $symbol = $request->query('symbol');
        $orders = $this->repository->getOpenOrdersBySymbol($symbol);

        return response()->json($orders);
    }

    #[OA\Get(
        path: '/api/user_orders',
        summary: 'Get user orders',
        description: 'Returns all user orders for orderbook ',
        operationId: 'userOrders',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
       
        responses: [
            new OA\Response(
                response: 200,
                description: 'Open orders retrieved successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Order')
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
    public function userOrders(Request $request): JsonResponse
    {
        $orders = $this->repository->getMyOrders();

        return response()->json($orders);
    }

  
    #[OA\Post(
        path: '/api/orders',
        summary: 'Create limit order',
        description: 'Creates a limit order and locks the required USD or assets',
        operationId: 'createLimitOrder',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['user_id', 'symbol', 'side', 'price', 'amount'],
                properties: [
                    new OA\Property(property: 'user_id', type: 'integer', example: 1),
                    new OA\Property(property: 'symbol', type: 'string', example: 'BTC'),
                    new OA\Property(property: 'side', type: 'string', enum: ['buy', 'sell'], example: 'buy'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 50000.00),
                    new OA\Property(property: 'amount', type: 'number', format: 'float', example: 0.50),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Order created successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/Order')
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request (e.g., insufficient balance)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Insufficient USD balance.'),
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
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function store(OrderRequest $request): JsonResponse
    {
        try {
            $order = $this->repository->createOrder($request->validated());

            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

   
    #[OA\Post(
        path: '/api/orders/{id}/cancel',
        summary: 'Cancel order',
        description: 'Cancels an open order and releases locked USD or assets',
        operationId: 'cancelOrder',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'Order ID',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order cancelled successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/Order')
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request (e.g., order is not open)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Order is not open and cannot be cancelled.'),
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
            new OA\Response(
                response: 404,
                description: 'Order not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Order not found.'),
                    ]
                )
            ),
        ]
    )]
    public function cancel(int $id): JsonResponse
    {
        try {
            $order = $this->repository->cancelOrder($id);

            return response()->json($order);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Order not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get available trading symbols
     *
     * Returns a list of all available trading symbols.
     */
    #[OA\Get(
        path: '/api/symbols',
        summary: 'Get available symbols',
        description: 'Returns a list of all available trading symbols',
        operationId: 'getSymbols',
        tags: ['Orders'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Symbols retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'symbols',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'name', type: 'string', example: 'BTC'),
                                    new OA\Property(property: 'value', type: 'string', example: 'BTC'),
                                ]
                            ),
                            example: [
                                ['name' => 'BTC', 'value' => 'BTC'],
                                ['name' => 'ETH', 'value' => 'ETH'],
                                ['name' => 'SOL', 'value' => 'SOL'],
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function getSymbols(): JsonResponse
    {
        return response()->json(
             $this->repository->getSymbols()
        , 200);
    }
}

