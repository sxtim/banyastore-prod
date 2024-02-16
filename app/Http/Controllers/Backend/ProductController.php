<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ProductRequest;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    const DIR_PRODUCTS = 'public/products/';

    public function index(): View
    {
        $products = Product::paginate(15);
        return view('backend.shop.product.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('backend.shop.product.create', compact('categories'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::all();
        return view('backend.shop.product.create',
            compact('categories', 'product')
        );
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension();
        $filename = uniqid() . '.' . $extension;
        Storage::put(self::DIR_PRODUCTS . $filename, File::get($image));

        $data = [
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'price' => $request->input('price'),
            'sort' => $request->input('sort'),
            'image' => self::DIR_PRODUCTS . $filename
        ];

        $valueProperties = $request->input('properties');

        DB::transaction(function () use ($data, $valueProperties) {
            $product = Product::create($data);
            if ($valueProperties) {
                $product->propertiesValues()->attach($valueProperties);
            }

        });

        return redirect()->route('backend.product.index')->with('success', 'Изменения сохранены');
    }

    public function update(Product $product, ProductRequest $request)
    {
        $data = [
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'price' => $request->input('price'),
            'sort' => $request->input('sort'),
        ];

        $image = $request->file('image');
        if ($image) {
            Storage::delete($product->image);
            $extension = $image->getClientOriginalExtension();
            $filename = uniqid() . '.' . $extension;
            Storage::put(self::DIR_PRODUCTS . $filename, File::get($image));
            $data['image'] = self::DIR_PRODUCTS . $filename;
        }

        $valueProperties = $request->input('properties');

        DB::transaction(function () use ($product, $data, $valueProperties) {
            $product->update($data);
            if ($valueProperties) {
                $product->propertiesValues()->detach();
                $product->propertiesValues()->attach($valueProperties);
            }

        });

        return redirect()->route('backend.product.index')->with('success', 'Данные обновлены');
    }
}
