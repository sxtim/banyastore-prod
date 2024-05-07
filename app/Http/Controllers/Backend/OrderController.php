<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\DiscountRequest;
use App\Models\Order\Order;
use App\Models\Shop\Discount;
use App\Models\Shop\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['status','user','deliveryVariant','paymentVariant','status'])->paginate(15);
        return view('backend.order.index', compact('orders'));
    }

    public function create(): View
    {
        $products = Product::all();
        return view('backend.discount.create', compact('products'));
    }
}
