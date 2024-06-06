<?php

namespace App\Services\Order;



use App\Models\Order\Order;
use App\Models\Order\OrderStatus\OrderStatus;
use App\Models\Order\OrderStatus\OrderStatusChange;
use Illuminate\Support\Facades\Auth;

class OrderStatusChangeService
{

    public function __construct()
    {
    }

    public function setStatus(int $orderId, int $statusValue): bool
    {
        $status = OrderStatus::find($statusValue);
        if (!$status) {
            throw new \Exception('Invalid status');
        }

        $order = Order::find($orderId);
        if (!$order) {
            throw new \Exception('Invalid order');
        }


        if ($order->status->id !== $status->id) {
            return $this->saveLog($order, $status);
        }

        return false;
    }


    private function saveLog(Order $order, OrderStatus $status): bool
    {
        $user = Auth::getUser();

        OrderStatusChange::insert([
            'order_id' => $order->id,
            'old_status_id' => $order->status_id,
            'new_status_id' => $status->id,
            'changed_user_id' => $user ? $user->id :  null,
            'changed_at' => now()
        ]);

        $order->update([
            'status_id' => $status->id
        ]);

        return true;
    }
}
