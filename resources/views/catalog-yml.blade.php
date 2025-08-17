<yml_catalog date="{{ $dateFile }}">
    <shop>
        <categories>
            @foreach($categories as $category)
                <category id="{{ $category->id }}" @if ($category->parent_id) parentId="{{ $category->parent_id }}"  @endif>{{ $category->name }}</category>
            @endforeach
        </categories>
        <offers>
            @foreach($products as $product)
                <offer id="{{ $product->id }}">
                    <name>{{ $product->name }}</name>
                    @if ($product->description && isset($product->description['blocks']))
                        <description>
                            @foreach($product->description['blocks'] as $block)
                                @if ($block['type'] == 'header' && isset($block['data']['level']))
                                    {!! str_replace('&nbsp;', ' ', strip_tags($block['data']['text'])) !!}
                                @endif

                                @if ($block['type'] == 'paragraph')
                                        {!! str_replace('&nbsp;', ' ', strip_tags($block['data']['text'])) !!}
                                @endif
                           @endforeach
                        </description>
                    @endif
                    @foreach($product->propertiesValues as $value)
                        @if($value->property->name === 'Вес')
                            <weight>{{ $value->name }}</weight>
                        @endif
                        @if($value->property->name === 'ДхШхВ')
                            <dimensions>{{ $value->name }}</dimensions>
                        @endif
                    @endforeach
                    <url>{{ route('products.detail', ['slug' => $product->slug]) }}</url>
                    <picture>{{ Request::getSchemeAndHttpHost() }}{{ Storage::url($product->image) }}</picture>
                    <price>{{ $product->getCurrentPrice() }}</price>
                    @if ($product->price > $product->getCurrentPrice())
                        <oldprice>{{ $product->price }}</oldprice>
                    @endif
                    <currencyId>RUR</currencyId>
                    <categoryId>{{ $product->category_id }}</categoryId>
                    @foreach($product->propertiesValues as $value)
                        <param name="{{ $value->property->name }}">{{ $value->name }}</param>
                    @endforeach
                </offer>
            @endforeach
        </offers>
    </shop>
</yml_catalog>
