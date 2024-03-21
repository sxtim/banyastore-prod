<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\DiscountRequest;
use App\Models\Shop\Discount;
use App\Models\Shop\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DiscountController extends Controller
{
    public function index(): View
    {
        $discounts = Discount::paginate(15);
        return view('backend.discount.index', compact('discounts'));
    }

    public function create(): View
    {
        $products = Product::all();
        return view('backend.discount.create', compact('products'));
    }

    public function edit(int $discountId): View
    {
        $products = Product::all();
        $discount = Discount::find($discountId);
        $discount->load(['products']);

        return view('backend.discount.edit',
            compact('discount','products')
        );
    }

    public function store(DiscountRequest $request): RedirectResponse
    {
        $data = [
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'discount' => $request->input('discount'),
            'is_active' => $request->boolean('is_active'),
        ];

        $products = $request->input('products');

        DB::transaction(function () use ($data, $products) {
            $discount = Discount::create($data);
            if ($products) {
                Product::whereIn('id', $products)->update(['discount_id' => $discount->id]);
            }
        });

        return redirect()->route('backend.discount.index')->with('success', 'Изменения сохранены');
    }

    public function update(int $discountId, DiscountRequest $request): RedirectResponse
    {
        $discount = Discount::findOrFail($discountId);

        $data = [
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'discount' => $request->input('discount'),
            'is_active' => $request->boolean('is_active'),
        ];

        $products = $request->input('products');

        DB::transaction(function () use ($discount, $data, $products) {
            $discount->update($data);
            if ($products) {
                Product::where('discount_id', $discount->id)->update(['discount_id' => null]);
                Product::whereIn('id', $products)->update(['discount_id' => $discount->id]);
            }
        });

        return redirect()->route('backend.discount.index')->with('success', 'Изменения сохранены');
    }
}
