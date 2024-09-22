@csrf
<div class="row">
    <div class="col-4">
        <label for="name" class="main-label">Название скидки</label>
        <input type="text" name="name" id="name" class="form-control" autocomplete="off"
               value="{{ old('name', ( isset($discount) ? $discount->name : '')) }}">
    </div>
    <div class="col-2">
        <label for="is_active">Активность ?</label>
        <div>
            <span>Да</span>
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', ( isset($discount) && $discount->is_active == 1 ? 'checked' : '')) }}>
        </div>
    </div>
    <div class="col-3">
        <label for="start_at" class="main-label">Тип</label>
        <select class="form-control" name="type">
            <option value="percent" {{ isset($discount) && $discount->type === 'percent' ? 'selected' : '' }}>
                Проценты
            </option>
            <option value="rub" {{ isset($discount) && $discount->type === 'rub' ? 'selected' : '' }}>
                Рубли
            </option>
        </select>
    </div>
    <div class="col-3">
        <label for="discount" class="main-label">Размер скидки скидки</label>
        <input type="number" name="discount" id="discount" class="form-control" autocomplete="off"
               value="{{ old('discount', ( isset($discount) ? $discount->discount : '')) }}">
    </div>
</div>
<div class="row mt-4">
    <div id="discount-products-wrapper" class="mt-4">
        <div class="col-12">
            <label for="discount-products">
                Выберите товары к которым надо применить скидку
            </label>
            <div></div>
            <select multiple="multiple" name="products[]" id="discount-products" class="form-control" style="height:700px;">
                @foreach($products as $element)
                    <option @if(isset($discount) && $discount->products->where('id',$element->id)->first()) selected @endif
                    value="{{ $element->id }}">
                        {{ $element->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="row justify-content-end mt-4">
    <div class="form-group col-md-6 btn-wrap">
        <button type="submit" class="btn btn-primary mr-4">Сохранить</button>
        <a href="{{ route('backend.discount.index') }}" class="btn btn-secondary">Отмена</a>
    </div>
</div>
