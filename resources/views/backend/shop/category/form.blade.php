    @csrf
    <div class="row mkedr-form">
        <div class="col-12">
            <label for="name" class="main-label">Название категории</label>
            <input type="text" name="name" id="name" class="form-control" autocomplete="off"
                   value="{{ old('name', ( isset($category) ? $category->name : '')) }}" required>
        </div>
    </div>
    <div class="row mkedr-form">
        <div class="col-sm-4">
            <label for="parent_id" class="main-label">
                Родительская категория: {{ isset($category) && isset($category->parent->name) ? $category->parent->name : '' }}
            </label>
            <select name="parent_id" id="parent_id" class="form-control">
                <option value="">Корневая категория</option>
                @foreach($categories as $categoryItem)
                    <option value="{{ $categoryItem->id }}" {{ isset($category) && $categoryItem->id == $category->parent_id ? 'selected' : '' }}>
                        {{ $categoryItem->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-4">
            <label  for="sort" class="main-label">Сортировка</label>
            <input type="number" name="sort" id="sort" class="form-control" autocomplete="off"
                   value="{{ old('sort', ( isset($category) ? $category->sort : '')) }}" required>
        </div>

    </div>

    <div class="row justify-content-end">
        <div class="form-group col-md-6 btn-wrap">
            <button type="submit" class="btn btn-primary mr-4">Сохранить</button>
            <a href="{{ route('backend.categories.index') }}" class="btn btn-secondary">Отмена</a>
        </div>
    </div>
