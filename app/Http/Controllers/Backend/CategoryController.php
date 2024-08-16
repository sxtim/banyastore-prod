<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\CategoryRequest;
use App\Models\Shop\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    const DIR_CATEGORIES = 'public/categories/';

    public function index(): View
    {
        $categories = Category::with(['subcategory'])->where('parent_id', null)->get();
        return view('backend.shop.category.index', compact('categories'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('backend.shop.category.create', compact('categories'));
    }

    public function edit(int $categoryId): View
    {
        $category = Category::find($categoryId);
        $categories = Category::all();
        return view('backend.shop.category.edit',
            compact('category', 'categories')
        );
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $image = $request->file('image');
        if ($image) {
            $extension = $image->getClientOriginalExtension();
            $filename = uniqid() . '.' . $extension;
            Storage::put(self::DIR_CATEGORIES . $filename, File::get($image));
        }

        Category::create([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id') ? $request->input('parent_id') : null,
            'sort' => $request->input('sort'),
            'image' => $image ? self::DIR_CATEGORIES . $filename : null,
            'is_active' => true
        ]);

        return redirect()->route('backend.categories.index')->with('success', 'Изменения сохранены');
    }

    public function update(int $categoryId, CategoryRequest $request): RedirectResponse
    {
        $category = Category::findOrFail($categoryId);
        $data = [
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id') ? $request->input('parent_id') : null,
            'sort' => $request->input('sort'),
            'is_active' => true
        ];

        $image = $request->file('image');
        if ($image) {
            if ($category->image) {
                Storage::delete($category->image);
            }
            $extension = $image->getClientOriginalExtension();
            $filename = uniqid() . '.' . $extension;
            Storage::put(self::DIR_CATEGORIES . $filename, File::get($image));
            $data['image'] = self::DIR_CATEGORIES . $filename;
        }

        $category->update($data);

        return redirect()->route('backend.categories.index')->with('success', 'Данные обновлены');
    }
}
