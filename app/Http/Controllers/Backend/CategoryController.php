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
        $categories = Category::all();
        return view('backend.shop.category.index', compact('categories'));
    }

    public function create(): View
    {
        return view('backend.shop.category.create');
    }

    public function edit(Category $category): View
    {
        return view('backend.shop.category.create',
            compact('category')
        );
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id') ? $request->input('parent_id') : null,
            'sort' => $request->input('sort'),
        ]);

        return redirect()->route('backend.category.index')->with('success', 'Изменения сохранены');
    }

    public function update(Category $category, CategoryRequest $request): RedirectResponse
    {
        $category->update([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id') ? $request->input('parent_id') : null,
            'sort' => $request->input('sort'),
        ]);

        return redirect()->route('backend.category.index')->with('success', 'Данные обновлены');
    }
}
