<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Models\Order\OrderStatus\OrderStatus;
use App\Models\User;
use App\Services\Order\OrderService;
use App\Services\Order\OrderStatusChangeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['status','user','deliveryVariant','paymentVariant','status'])->paginate(15);
        return view('backend.order.index', compact('orders'));
    }

    public function show(int $id): View
    {
        $statuses = OrderStatus::all();
        $order = Order::findOrFail($id);
        $order->load(['products','user','deliveryVariant','paymentVariant','deliveries','status']);
        return view('backend.order.show', compact('order','statuses'));
    }

    public function updateUser(int $id, Request $request): JsonResponse
    {
        $order = Order::findOrFail($id);
        $user = User::findOrFail($request->input('user_id'));
        $order->update([
            'user_id' => $user->id
        ]);

        return response()->json([
            'status' => 'success',
            'name' => $user->getFullName()
        ]);
    }

    public function updateStatus(int $id, Request $request, OrderStatusChangeService $service): JsonResponse
    {
        try{
            $service->setStatus($id, $request->input('status_id'));
            return response()->json([
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updatePrice(int $id, Request $request, OrderService $orderService): JsonResponse
    {
        try{
            $orderService->updatePrice(
                orderId: $id,
                productId: $request->input('product_id'),
                price: $request->input('price')
            );

            return response()->json([
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
