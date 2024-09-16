@csrf
<div class="row">
    <div class="col-6">
        <label for="category" class="main-label">Категория</label>
        <select class="form-control" name="category">
            <option>
                Не выбрано
            </option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ isset($seoTemplate) && $seoTemplate->category_id === $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-3">
        <label for="is_main">По умолчанию ?</label>
        <div>
            <span>Да</span>
            <input type="checkbox" id="is_main" name="is_main" value="1" {{ old('is_main', ( isset($seoTemplate) && $seoTemplate->is_main == 1 ? 'checked' : '')) }}>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-6">
        <label for="category" class="main-label">Материал</label>
        <select class="form-control" name="type_material">
            @foreach($listTypeMaterial as $key => $material)
                <option value="{{ $key }}" {{ isset($seoTemplate) && $seoTemplate->type_material === $key ? 'selected' : '' }}>
                    {{ $material }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-6">
        <label for="category" class="main-label">Тип шаблона</label>
        <select class="form-control" name="type_template">
            <option value="title" {{ isset($seoTemplate) && $seoTemplate->type_template === 'title' ? 'selected' : '' }}>
                title
            </option>
            <option value="description" {{ isset($seoTemplate) && $seoTemplate->type_template === 'description' ? 'selected' : '' }}>
                description
            </option>
        </select>
    </div>
</div>
<div id="seo-template">
    @if(isset($seoTemplate) && $seoTemplate->type_template)
        <seo-template-component
            :text-template-prop='"{{ $seoTemplate->text_template }}"'
            :properties='@json($properties)'
        ></seo-template-component>
    @else
        <seo-template-component
            :properties='@json($properties)'
        ></seo-template-component>
    @endif
</div>



<div class="row justify-content-end mt-4">
    <div class="form-group col-md-6 btn-wrap">
        <button type="submit" class="btn btn-primary mr-4">Сохранить</button>
        <a href="{{ route('backend.seo.index') }}" class="btn btn-secondary">Отмена</a>
    </div>
</div>
