<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\PropertyValueRequest;
use App\Models\Shop\Property\Property;
use App\Models\Shop\Property\PropertyValue;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PropertyValueController extends Controller
{
    public function index(int $propertyId): View
    {
        $property = Property::findOrFail($propertyId);
        $values = PropertyValue::where('property_id', $propertyId)->paginate(15);
        return view('backend.shop.property-values.index', compact('values','property'));
    }

    public function create(int $propertyId): View
    {
        $property = Property::findOrFail($propertyId);
        return view('backend.shop.property-values.create', compact('property'));
    }

    public function edit(int $propertyValueId): View
    {
        $propertyValue = PropertyValue::findOrFail($propertyValueId);
        return view('backend.shop.property-values.edit',
            compact('propertyValue')
        );
    }

    public function store(int $propertyId, PropertyValueRequest $request): RedirectResponse
    {
        $property = Property::findOrFail($propertyId);
        PropertyValue::create([
            'name' => $request->input('name'),
            'property_id' => $property->id,
        ]);

        return redirect()->route('backend.property-values.index', ['propertyId' => $propertyId])->with('success', 'Изменения сохранены');
    }

    public function update(int $propertyValueId, PropertyValueRequest $request): RedirectResponse
    {
        $propertyValue = PropertyValue::findOrFail($propertyValueId);
        $propertyValue->update([
            'name' => $request->input('name')
        ]);

        return redirect()->route('backend.property-values.index', ['propertyId' => $propertyValue->property->id])->with('success', 'Данные обновлены');
    }
}
