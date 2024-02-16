<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\PropertyRequest;
use App\Models\Shop\Property\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PropertyController extends Controller
{
    public function index(): View
    {
        $properties = Property::all();
        return view('backend.shop.property.index', compact('properties'));
    }

    public function create(): View
    {
        return view('backend.shop.property.create');
    }

    public function edit(Property $property): View
    {
        return view('backend.shop.property.create',
            compact('property')
        );
    }

    public function store(PropertyRequest $request): RedirectResponse
    {
        Property::create([
            'name' => $request->input('name'),
            'is_required' => $request->boolean('is_required'),
        ]);

        return redirect()->route('backend.property.index')->with('success', 'Изменения сохранены');
    }

    public function update(Property $property, PropertyRequest $request): RedirectResponse
    {
        $property->update([
            'name' => $request->input('name'),
            'is_required' => $request->boolean('is_required'),
        ]);

        return redirect()->route('backend.property.index')->with('success', 'Данные обновлены');
    }
}
