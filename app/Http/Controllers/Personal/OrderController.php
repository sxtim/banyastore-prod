<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Personal\PasswordRequest;
use App\Http\Requests\Personal\PersonalRequest;
use App\Models\Order\Order;
use App\Models\Shop\Product;
use App\Models\User;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class OrderController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $orders = Order::with(['deliveryVariant','paymentVariant','deliveries'])->where('user_id','=', $user->id)->get();
        $activeMenu = 'orders';
        return view('personal.order.index',compact('user','orders', 'activeMenu'));
    }

    public function detail(int $id): View
    {
        $order = Order::findOrFail($id);
        $order->load(['products','user','deliveryVariant','paymentVariant','deliveries','status']);
        $activeMenu = 'orders';
        return view('personal.order.detail', compact('order', 'activeMenu'));
    }

    public function pay(
        int $id,
        PaymentService $paymentService
    ): RedirectResponse
    {
        $order = Order::findOrFail($id);
        $response = $paymentService->pay($order);
        if ($response->getUrl()) {
            return redirect()->away($response->getUrl());
        }

        if ($response->getIsSuccess() === true) {
            return redirect()->route('order.success');
        }

        return redirect()->route('order.error');
    }
}
