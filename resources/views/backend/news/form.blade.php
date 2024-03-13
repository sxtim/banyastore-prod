@csrf
<div class="row">
    <div class="col-4">
        <label for="name" class="main-label">Название новости</label>
        <input type="text" name="name" id="name" class="form-control" autocomplete="off"
               value="{{ old('name', ( isset($news) ? $news->name : '')) }}">
    </div>
    <div class="col-2">
        <label for="is_active">Активность ?</label>
        <div>
            <span>Да</span>
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', ( isset($news) && $news->is_active == 1 ? 'checked' : '')) }}>
        </div>
    </div>
    <div class="col-3">
        <label for="start_at" class="main-label">Начало новости</label>
        <input type="datetime-local" name="start_at" id="start_at" class="form-control" autocomplete="off"
               value="{{ old('start_at', ( isset($news) ? $news->start_at : '')) }}">
    </div>
    <div class="col-3">
        <label for="end_at" class="main-label">Конец новости</label>
        <input type="datetime-local" name="end_at" id="end_at" class="form-control" autocomplete="off"
               value="{{ old('end_at', ( isset($news) ? $news->end_at : '')) }}">
    </div>
</div>
<div class="row mt-4">
    <div class="col-4">
        <label for="sort" class="main-label">Сортировка</label>
        <input type="number" name="sort" id="sort" class="form-control" autocomplete="off"
               value="{{ old('sort', ( isset($news) ? $news->sort : '')) }}">
    </div>
    <div class="col-4">
        <label for="btn" class="main-label">Текст на кнопке</label>
        <input type="text" name="btn" id="btn" class="form-control" autocomplete="off"
               value="{{ old('btn', ( isset($news) ? $news->btn : '')) }}">
    </div>
    <div class="col-4">
        <label for="link_btn" class="main-label">Ссылка для кнопки</label>
        <input type="text" name="link_btn" id="link_btn" class="form-control" autocomplete="off"
               value="{{ old('link_btn', ( isset($news) ? $news->link_btn : '')) }}">
    </div>
</div>
<div class="row mt-4">
    <div class="col-12">
        <label for="preview_text" class="main-label">Краткое описание</label>
        <textarea type="text" name="preview_text" id="preview_text" class="form-control" autocomplete="off">{{ isset($news) ? $news->preview_text : '' }}</textarea>
    </div>
</div>
<div class="row mt-4">
    <div class="col-12">
        <label for="detail_text" class="main-label">Детальное описание</label>
        <div id="editorjs"></div>
        <input type="hidden" id="detail_text" name="detail_text"/>
    </div>
</div>
@isset($news)
    <div class="row mt-3">
        <div class="col-4">
            <img src="{{ $news->main_img }}" alt="" class="img-fluid">
        </div>
        <div class="col-4">
            <img src="{{ $news->preview_img }}" alt="" class="img-fluid">
        </div>
    </div>
@endisset
<div class="row mt-3">
    <div class="col-4">
        <label for="preview_image" class="main-label">Превью изображение</label>
        <input type="file" name="preview_image" id="preview_image" class="form-control-file" autocomplete="off">
    </div>
    <div class="col-4">
        <label for="main_image" class="main-label">Детальное изображение</label>
        <input type="file" name="main_image" id="main_image" class="form-control-file" autocomplete="off">
    </div>
</div>
<div class="row justify-content-end mt-4">
    <div class="form-group col-md-6 btn-wrap">
        <button type="submit" class="btn btn-primary mr-4">Сохранить</button>
        <a href="{{ route('backend.news.index') }}" class="btn btn-secondary">Отмена</a>
    </div>
</div>
