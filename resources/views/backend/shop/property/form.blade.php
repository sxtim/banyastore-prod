@csrf
<div class="row">
    <div class="col-4">
        <label for="name" class="main-label">Название свойства</label>
        <input type="text" name="name" id="name" class="form-control" autocomplete="off"
               value="{{ old('name', ( isset($property) ? $property->name : '')) }}">
    </div>
    <div class="col-sm-4">
        <label for="is_required">Обязательное ?</label>
        <div>
            <span>Да</span>
            <input type="checkbox" id="is_required" name="is_required" value="1" {{ old('is_required', ( isset($property) && $property->is_required == 1 ? 'checked' : '')) }}>
        </div>

    </div>
</div>
<div class="row justify-content-end">
    <div class="form-group col-md-6 btn-wrap">
        <button type="submit" class="btn btn-primary mr-4">Сохранить</button>
        <a href="{{ route('backend.product.index') }}" class="btn btn-secondary">Отмена</a>
    </div>
</div>
