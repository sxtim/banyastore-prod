@csrf
<div class="row">
    <div class="col-4">
        <label for="name" class="main-label">Название товара</label>
        <input type="text" name="name" id="name" class="form-control" autocomplete="off"
               value="{{ old('name', ( isset($product) ? $product->name : '')) }}">
    </div>
    <div class="col-4">
        <label for="price" class="main-label">Цена</label>
        {{ isset($product) && $product->getCurrentPrice() != $product->price ? '(Со скидкой: '. $product->getCurrentPrice().')' : ''}}
        <input type="text" name="price" id="price" class="form-control" autocomplete="off"
               value="{{ old('price', ( isset($product) ? $product->price : '')) }}">
    </div>
    <div class="col-4">
        <label for="sort" class="main-label">Активность</label>
        <div>
            <span>Да?</span>
            <input type="checkbox" name="is_active" id="is_active" autocomplete="off"
                   {{ isset($product) && $product->is_active ? 'checked' : '' }}>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-6">
        <label for="category" class="main-label">Категория</label>
        <select class="form-control" name="category">
            <option value="0">
                Не выбрано
            </option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ isset($product) && $product->category_id === $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-6">
        <label for="tag" class="main-label">Плашка</label>
        <select class="form-control" name="tag">
            <option value="">
                Не выбрано
            </option>
            @foreach($tags as $key => $tag)
                <option value="{{ $key }}" {{ isset($product) && $product->tag === $key ? 'selected' : '' }}>
                    {{ $tag }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="row mt-3">
    <div class="col-6">
        <label for="preview_text" class="main-label">Краткое описание</label>
        <div id="editorjs-preview"></div>
        <input type="hidden" id="preview_text" name="preview_text"/>
    </div>
    <div class="col-6">
        <label for="description" class="main-label">Описание</label>
        <div id="editorjs"></div>
        <input type="hidden" id="description" name="description"/>
    </div>
</div>


@isset($product)
    <div class="row mt-3">
        <div class="col-4">
            <img src="{{ $product->getImageUrlAttribute() }}" alt="" class="img-fluid" style="max-width:150px;">
        </div>
        @if ($product->additionalImages)
            <div class="col-4">
                @foreach($product->additionalImages as $image)
                    <span style="display: inline-block;position: relative">
                        <img src="{{ $image->getImageUrlAttribute() }}" alt="" class="img-fluid" style="max-width:150px;">
                        <span style="text-decoration: underline; cursor:pointer; color: #03a2ff" id="del-additional-image" param="{{ $image->id }}">
                            Удалить
                        </span>
                    </span>
                @endforeach
            </div>
        @endif
    </div>
@endisset
<div class="row mt-3">
    <div class="col-4">
        <label for="image" class="main-label">Изображение продукта</label>
        <input type="file" name="image" id="image" class="form-control-file" autocomplete="off">
    </div>
    <div class="col-4">
        <label for="additional-images" class="main-label">Изображения для слайдера</label>
        <input type="file" name="additional-images[]" id="additional-images" class="form-control-file" autocomplete="off" multiple>
    </div>
    <div class="col-2">
        <label for="sort" class="main-label">Сортировка</label>
        <input type="number" name="sort" id="sort" class="form-control" autocomplete="off"
               value="{{ old('sort', ( isset($product) ? $product->sort : 0)) }}">
    </div>
</div>


<div class="row mt-3">
    <h4>Свойства</h4>
</div>
<div class="row mt-3">
    @foreach($properties as $property)
        <div class="col-3">
            <label for="properties" class="main-label">
                {{ $property->name }}
            </label>
            <select name="properties[]" class="form-control">
                <option value="">
                    Не выбрано
                </option>
                @foreach($property->values as $value)
                    <option value="{{ $value->id }}" {{ isset($product) && $product->propertiesValues->where('id', $value->id)->first() ? 'selected' : '' }}>
                        {{ $value->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endforeach
</div>
<div class="row justify-content-end">
    <div class="form-group col-md-6 btn-wrap">
        <button type="submit" class="btn btn-primary mr-4">Сохранить</button>
        <a href="{{ route('backend.product.index') }}" class="btn btn-secondary">Отмена</a>
    </div>
</div>
