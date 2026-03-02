<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Contracts\OrderServiceInterface;
use App\Jobs\SendOrderCreatedEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends BaseController
{
    private OrderServiceInterface $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * List orders belonging to authenticated user
     */
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="List orders belonging to authenticated user",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Order list returned successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $orders = $this->orderService->getOrdersForUser(auth()->id(), $perPage);
        return $this->success(OrderResource::collection($orders), 'Orders retrieved successfully');
    }

    /**
     * Show detail of a specific order belonging to authenticated user
     */
    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get details of an order",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Order details returned"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Not found or not belong to user")
     * )
     */
    public function show($id)
    {
        $order = $this->orderService->getOrderForUser($id, auth()->id());
        return $this->success(new OrderResource($order), 'Order retrieved successfully');
    }

    /**
     * Create a new order
     *
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create order",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"items"},
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="product_id", type="integer"),
     *                         @OA\Property(property="quantity", type="integer")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Order created successfully"),
     *     @OA\Response(response=400, description="Invalid data"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(OrderRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $order = $this->orderService->createOrder($data);

        // dispatch a queued job to send confirmation email after the transaction commits
        SendOrderCreatedEmail::dispatch($order)->afterCommit();

        return $this->success(new OrderResource($order), 'Order created successfully', Response::HTTP_CREATED);
    }
}
