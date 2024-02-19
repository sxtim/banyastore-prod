@foreach($subcategories as $subcategory)
    <ul  style="margin: 5px 0">
        <li>
            <a href="{{ route('backend.categories.edit', ['category' => $subcategory->id]) }}">
                {{$subcategory->name}}
            </a>
        </li>
        @if(count($subcategory->subcategory))
            @include('backend.shop.category.sub_category_list',['subcategories' => $subcategory->subcategory])
        @endif
    </ul>
@endforeach
