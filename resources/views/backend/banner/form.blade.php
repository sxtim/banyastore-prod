@csrf
<div class="row">
    <div class="col-4">
        <label for="name" class="main-label">Название баннера</label>
        <input type="text" name="name" id="name" class="form-control" autocomplete="off"
               value="{{ old('name', ( isset($banner) ? $banner->name : '')) }}">
    </div>
    <div class="col-2">
        <label for="is_active">Активность ?</label>
        <div>
            <span>Да</span>
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', ( isset($banner) && $banner->is_active == 1 ? 'checked' : '')) }}>
        </div>
    </div>
    <div class="col-4">
        <label for="link" class="main-label">Ссылка</label>
        <input type="text" name="link" id="link" class="form-control" autocomplete="off"
               value="{{ old('link', ( isset($banner) ? $banner->link : '')) }}">
    </div>
    <div class="col-2">
        <label for="sort" class="main-label">Сортировка</label>
        <input type="number" name="sort" id="sort" class="form-control" autocomplete="off"
               value="{{ old('sort', ( isset($banner) ? $banner->sort : '')) }}">
    </div>
</div>
@isset($banner)
    <div class="row mt-3">
        <div class="col-4">
            <img src="{{ $banner->getUrlImage() }}" alt="" class="img-fluid">
        </div>
    </div>
@endisset
<div class="row mt-3">
    <div class="col-4">
        <label for="image" class="main-label">Изображение</label>
        <input type="file" name="image" id="image" class="form-control-file" autocomplete="off">
    </div>
</div>
<div class="row justify-content-end mt-4">
    <div class="form-group col-md-6 btn-wrap">
        <button type="submit" class="btn btn-primary mr-4">Сохранить</button>
        <a href="{{ route('backend.banner.index') }}" class="btn btn-secondary">Отмена</a>
    </div>
</div>
