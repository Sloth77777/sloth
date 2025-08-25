<?php

declare(strict_types=1);

namespace App\Parts\API\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Parts\API\Requests\Order\StoreOrderRequest;
use App\Parts\API\Resources\Order\OrderResource;
use App\Services\Cart\CartResolver;
use App\Services\Orders\OrderCreator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(
        private readonly CartResolver $cartResolver,
        private readonly OrderCreator $orderCreator,
    ) {
    }

    public function index(Request $request):AnonymousResourceCollection | JsonResponse
    {
        if ($request->user()) {
            $orders = Order::query()
                ->where('user_id', $request->user()->id)
                ->latest('id')
                ->with('items')
                ->paginate(20);
        } else {
            $guestToken = $this->cartResolver->currentGuestToken($request);
            if (!$guestToken) {
                return response()->json(['data' => []]);
            }
            $orders = Order::query()
                ->where('guest_token', $guestToken)
                ->latest('id')
                ->with('items')
                ->paginate(20);
        }

        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request): OrderResource|JsonResponse
    {
        try {
            $order = $this->orderCreator->createFromCart($request->validated());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return OrderResource::make($order);
    }
    public function show(Request $request, Order $order): JsonResponse|OrderResource
    {
        if ($request->user()) {
            if ($order->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Not found'], 404);
            }
        } else {
            $guestToken = $this->cartResolver->currentGuestToken($request);
            if (!$guestToken || $order->guest_token !== $guestToken) {
                return response()->json(['message' => 'Not found'], 404);
            }
        }

        return OrderResource::make($order->load('items'));
    }
}
