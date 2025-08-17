<yml_catalog date="{{ $dateFile }}">
    <shop>
        <currencies>
            <currency id="RUB" rate="1"/>
        </currencies>
        <categories>
            @foreach($categories as $category)
                <category id="{{ $category->id }}" @if ($category->parent_id) parentId="{{ $category->parent_id }}"  @endif>{{ $category->name }}</category>
            @endforeach
        </categories>
        <offers>
            @foreach($products as $product)
                <offer id="{{ $product->id }}" available="true">
                    <name>
                        {{ $product->name }}
                    </name>
                    @if ($product->description && isset($product->description['blocks']))
                        <description>
                            @foreach($product->description['blocks'] as $block)
                                @if ($block['type'] == 'header' && isset($block['data']['level']))
                                    {!! str_replace('&nbsp;', ' ', $block['data']['text']) !!}
                                @endif

                                @if ($block['type'] == 'paragraph')
                                        {!! str_replace('&nbsp;', ' ', $block['data']['text']) !!}
                                @endif
                           @endforeach
                        </description>
                    @endif
                    <url>
                        {{ route('products.detail', ['slug' => $product->slug]) }}
                    </url>
                    <picture>
                        {{ Request::getSchemeAndHttpHost() }}{{ Storage::url($product->image) }}
                    </picture>
                    <price>
                        {{ $product->getCurrentPrice() }}
                    </price>
                    <currencyId>
                        RUB
                    </currencyId>
                    <categoryId>
                        {{ $product->category_id }}
                    </categoryId>
                    <country_of_origin>
                        Россия
                    </country_of_origin>
                    @foreach($product->propertiesValues as $value)
                        <param name="{{ $value->property->name }}">
                            {{ $value->name }}
                        </param>
                    @endforeach
                </offer>
            @endforeach
        </offers>
    </shop>
</yml_catalog>
