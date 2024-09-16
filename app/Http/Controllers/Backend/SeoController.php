<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\SeoTemplateRequest;
use App\Models\SeoTemplate;
use App\Models\Shop\Category;
use App\Models\Shop\Property\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SeoController extends Controller
{
    public function index(): View
    {
        $seoTemplates = SeoTemplate::with(['category'])->paginate(15);
        return view('backend.seo.index', compact('seoTemplates'));
    }

    public function create(): View
    {
        $listTypeMaterial = SeoTemplate::LIST_MATERIAL;
        $categories = Category::all();
        $properties = Property::all();
        return view('backend.seo.create', compact('listTypeMaterial','categories','properties'));
    }

    public function edit(int $templateId): View
    {
        $seoTemplate = SeoTemplate::find($templateId);
        $listTypeMaterial = SeoTemplate::LIST_MATERIAL;
        $categories = Category::all();
        $properties = Property::all();
        return view('backend.seo.edit',
            compact('seoTemplate', 'listTypeMaterial','categories','properties')
        );
    }

    public function store(SeoTemplateRequest $request): RedirectResponse
    {
        SeoTemplate::create([
            'category_id' => $request->input('category') ? $request->input('category') : null,
            'is_main' => $request->boolean('is_main'),
            'text_template' => $request->input('text_template'),
            'type_material' => $request->input('type_material'),
            'type_template' => $request->input('type_template')
        ]);

        return redirect()->route('backend.seo.index')->with('success', 'Изменения сохранены');
    }

    public function update(int $templateId, SeoTemplateRequest $request): RedirectResponse
    {
        $seoTemplate = SeoTemplate::findOrFail($templateId);

        $seoTemplate->update([
            'category_id' => $request->input('category') ? $request->input('category') : null,
            'is_main' => $request->boolean('is_main'),
            'text_template' => $request->input('text_template'),
            'type_material' => $request->input('type_material'),
            'type_template' => $request->input('type_template')
        ]);

        return redirect()->route('backend.seo.index')->with('success', 'Данные обновлены');
    }
}
