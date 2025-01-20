<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Filters\ProductFilter;
use App\Http\Requests\Backend\ImageRequest;
use App\Http\Requests\Backend\ProductRequest;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use App\Models\Shop\ProductFile;
use App\Models\Shop\Property\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    const DIR_PRODUCTS = 'public/products/';
    const DIR_OTHER_IMAGES_PRODUCTS = 'public/products/other/';

    public function index(ProductFilter $filter)
    {
        $products = Product::filter($filter)->with(['category'])->paginate(15);
        $categories = Category::all();
        $filters = $filter->filters();

        if ($filters) {
            return response(view('backend.shop.index',
                compact('products',
                    'filters',
                    'categories')
            ))->withCookie('productsFilterBackend',http_build_query($filters));
        }

        return response(view('backend.shop.index',
            compact('products',
                'filters',
                'categories')
        ))->withoutCookie('productsFilterBackend');
    }

    public function create(): View
    {
        $categories = Category::all();
        $properties = Property::with(['values'])->get();
        $tags = Product::LIST_TAG;
        return view('backend.shop.product.create', compact('categories','properties','tags'));
    }

    public function edit(Product $product): View
    {
        $product->load(['propertiesValues','discount','additionalImages']);
        $categories = Category::all();
        $properties = Property::with(['values'])->get();
        $tags = Product::LIST_TAG;
        return view('backend.shop.product.edit',
            compact('categories', 'product','properties','tags')
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

        $additionalImages = $request->file('additional-images');

        $data = [
            'name' => $request->input('name'),
            'description' => json_decode($request->input('description'), true),
            'preview_text' => $request->input('preview_text') ? json_decode($request->input('preview_text'), true) : null,
            'category_id' => $request->input('category'),
            'price' => $request->input('price'),
            'sort' => $request->input('sort'),
            'image' => $image ? self::DIR_PRODUCTS . $filename : '',
            'tag' => $request->input('tag'),
            'is_popular' => $request->boolean('is_popular'),
            'is_active' => $request->boolean('is_active')
        ];

        $valueProperties = [];
        foreach ($request->input('properties') as $prop) {
            if ($prop) {
                $valueProperties[] = $prop;
            }
        }

        DB::transaction(function () use ($data, $valueProperties, $additionalImages) {
            $product = Product::create($data);
            if ($valueProperties) {
                $product->propertiesValues()->attach($valueProperties);
            }

            //добавление дополнительных изображений
            if ($additionalImages) {
                foreach ($additionalImages as $file) {
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                    Storage::put(self::DIR_PRODUCTS . $filename, File::get($file));

                    $product->additionalImages()->create([
                        'file_path' => self::DIR_PRODUCTS . $filename,
                        'type' => ProductFile::TYPE_IMAGE
                    ]);
                }
            }
        });

        return redirect()->route('backend.product.index')->with('success', 'Изменения сохранены');
    }

    public function update(Product $product, ProductRequest $request): RedirectResponse
    {
        $data = [
            'name' => $request->input('name'),
            'description' => json_decode($request->input('description'), true),
            'preview_text' => $request->input('preview_text') ? json_decode($request->input('preview_text'), true) : null,
            'category_id' => $request->input('category'),
            'price' => $request->input('price'),
            'sort' => $request->input('sort'),
            'tag' => $request->input('tag'),
            'is_popular' => $request->boolean('is_popular'),
            'is_active' => $request->boolean('is_active')
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

        $additionalImages = $request->file('additional-images');

        DB::transaction(function () use ($product, $data, $valueProperties, $additionalImages) {
            $product->update($data);
            if ($valueProperties) {
                $product->propertiesValues()->detach();
                $product->propertiesValues()->attach($valueProperties);
            }

            //добавление дополнительных изображений
            if ($additionalImages) {
                foreach ($additionalImages as $file) {
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                    Storage::put(self::DIR_PRODUCTS . $filename, File::get($file));

                    $product->additionalImages()->create([
                        'file_path' => self::DIR_PRODUCTS . $filename,
                        'type' => ProductFile::TYPE_IMAGE
                    ]);
                }
            }
        });

        $paramsUrl = '';
        if ($request->cookie('productsFilterBackend')) {
            $paramsUrl = '?'.$request->cookie('productsFilterBackend');
        }

        return redirect()->route('backend.product.index', $paramsUrl)->with('success', 'Данные обновлены');
    }

    public function addImage(ImageRequest $request): JsonResponse
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

    public function deleteImage(ImageRequest $request): void
    {
        Storage::delete($request->input('file'));
    }

    public function deleteAdditionalImage(ImageRequest $request): JsonResponse
    {
        $productFile = ProductFile::findOrFail($request->input('id'));
        Storage::delete($productFile->file_path);
        $productFile->delete();
        return response()->json([
            'status' => 'success'
        ]);
    }
}
