<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\PropertyValueRequest;
use App\Models\Shop\Property\PropertyValue;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PropertyValueController extends Controller
{
    public function index(): View
    {
        $values = PropertyValue::all();
        return view('backend.shop.property-values.index', compact('values'));
    }

    public function create(): View
    {
        return view('backend.shop.property-values.create');
    }

    public function edit(PropertyValue $propertyValue): View
    {
        return view('backend.shop.property-values.create',
            compact('propertyValue')
        );
    }

    public function store(PropertyValueRequest $request): RedirectResponse
    {
        PropertyValue::create([
            'name' => $request->input('name'),
            'property_id' => $request->input('property_id'),
        ]);

        return redirect()->route('backend.property-values.index')->with('success', 'Изменения сохранены');
    }

    public function update(PropertyValue $propertyValue, PropertyValueRequest $request): RedirectResponse
    {
        $propertyValue->update([
            'name' => $request->input('name'),
            'property_id' => $request->input('property_id'),
        ]);

        return redirect()->route('backend.property-values.index')->with('success', 'Данные обновлены');
    }
}
