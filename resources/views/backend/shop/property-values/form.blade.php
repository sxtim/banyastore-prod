@csrf
<div class="row">
    <div class="col-4">
        <label for="name" class="main-label">Название свойства</label>
        <input type="text" name="name" id="name" class="form-control" autocomplete="off"
               value="{{ old('name', ( isset($propertyValue) ? $propertyValue->name : '')) }}">
    </div>
</div>
<div class="row justify-content-end">
    <div class="form-group col-md-6 btn-wrap">
        <button type="submit" class="btn btn-primary mr-4">Сохранить</button>
        @if (isset($propertyValue))
            <a href="{{ route('backend.property-values.index', ['propertyId' => $propertyValue->property->id]) }}" class="btn btn-secondary">Отмена</a>
        @else
            <a href="{{ route('backend.property-values.index', ['propertyId' => $property->id]) }}" class="btn btn-secondary">Отмена</a>
        @endif
    </div>
</div>
