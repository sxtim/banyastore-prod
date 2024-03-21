<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ProductRequest;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use App\Models\Shop\Property\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    const DIR_PRODUCTS = 'public/products/';
    const DIR_OTHER_IMAGES_PRODUCTS = 'public/products/other/';


    public function index(): View
    {
        $products = Product::paginate(15);
        return view('backend.shop.product.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::all();
        $properties = Property::with(['values'])->get();
        return view('backend.shop.product.create', compact('categories','properties'));
    }

    public function edit(Product $product): View
    {
        $product->load(['propertiesValues','discount']);
        $categories = Category::all();
        $properties = Property::with(['values'])->get();
        return view('backend.shop.product.edit',
            compact('categories', 'product','properties')
        );
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $image = $request->file('image');
        if ($image) {
            $extension = $image->getClientOriginalExtension();
            $filename = uniqid() . '.' . $extension;
            Storage::put(self::DIR_PRODUCTS . $filename, File::get($image));
        }


        $data = [
            'name' => $request->input('name'),
            'description' => json_decode($request->input('description'), true),
            'category_id' => $request->input('category'),
            'price' => $request->input('price'),
            'sort' => $request->input('sort'),
            'image' => $image ? self::DIR_PRODUCTS . $filename : ''
        ];

        $valueProperties = [];
        foreach ($request->input('properties') as $prop) {
            if ($prop) {
                $valueProperties[] = $prop;
            }
        }

        DB::transaction(function () use ($data, $valueProperties) {
            $product = Product::create($data);
            if ($valueProperties) {
                $product->propertiesValues()->attach($valueProperties);
            }

        });

        return redirect()->route('backend.product.index')->with('success', 'Изменения сохранены');
    }

    public function update(Product $product, ProductRequest $request): RedirectResponse
    {
        $data = [
            'name' => $request->input('name'),
            'description' => json_decode($request->input('description'), true),
            'category_id' => $request->input('category'),
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

        $valueProperties = [];
        foreach ($request->input('properties') as $prop) {
            if ($prop) {
                $valueProperties[] = $prop;
            }
        }

        DB::transaction(function () use ($product, $data, $valueProperties) {
            $product->update($data);
            if ($valueProperties) {
                $product->propertiesValues()->detach();
                $product->propertiesValues()->attach($valueProperties);
            }

        });

        return redirect()->route('backend.product.index')->with('success', 'Данные обновлены');
    }

    public function addImage(Request $request): JsonResponse
    {
        $extension = $request->file('image')->getClientOriginalExtension();
        $filename = uniqid() . '.' . $extension;
        $pathImage = self::DIR_OTHER_IMAGES_PRODUCTS . $filename;
        Storage::put($pathImage, File::get($request->file('image')));

        return response()->json([
            'file' => [
                'url' => Storage::url($pathImage),
                'filePath' => $pathImage,
            ]
        ]);
    }

    public function deleteImage(Request $request): void
    {
        Storage::delete($request->input('file'));
    }
}
