<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\CategoryRequest;
use App\Models\Shop\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
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
        Category::create([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id') ? $request->input('parent_id') : null,
            'sort' => $request->input('sort'),
            'is_active' => true
        ]);

        return redirect()->route('backend.categories.index')->with('success', 'Изменения сохранены');
    }

    public function update(int $categoryId, CategoryRequest $request): RedirectResponse
    {
        $category = Category::findOrFail($categoryId);
        $category->update([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id') ? $request->input('parent_id') : null,
            'sort' => $request->input('sort'),
            'is_active' => true
        ]);

        return redirect()->route('backend.categories.index')->with('success', 'Данные обновлены');
    }
}
