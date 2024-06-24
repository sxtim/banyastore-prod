@extends('layouts.app')

@section('pagetitle', $action->name.' | Banyastore')

@section('content')
    <section class="promotion-detail section">
        <div class="container">

            <h1 class="promotion-detail__title title-s">
                {{ $action->name }}
            </h1>
            <div class="promotion-detail__wrapper">
                <div class="promotion-detail__picture">
                    <img src="{{ $action->getMainImage() }}" alt="{{ $action->name }}">
                </div>
                <div class="promotion-detail__desc">
                    <div class="promotion-detail__txt">
                        @if ($action->detail_text && isset($action->detail_text['blocks']))
                            @foreach($action->detail_text['blocks'] as $block)
                                @if ($block['type'] == 'header' && isset($block['data']['level']))
                                    <h{{ $block['data']['level'] }}  >
                                        {!! $block['data']['text'] !!}
                                    </h{{ $block['data']['level'] }}>
                                @endif

                                @if ($block['type'] == 'paragraph')
                                    <p>
                                        {!! $block['data']['text'] !!}
                                    </p>
                                @endif

                                @if ($block['type'] == 'image' && isset($block['data']['file']['url']))
                                    <figure class="figure my-4" style="text-align: center">
                                        @if(isset($block['data']['link']) && $block['data']['link'])
                                            <a href="{{ $block['data']['link'] }}" target="_blank">
                                                <img loading="lazy" src="{{ $block['data']['file']['url'] }}"
                                                     class="figure-img img-fluid rounded"
                                                     width="825"
                                                    {{--                                 alt="{% if altExists %} {{ block.data.caption|raw|split('|')|last }}{% endif %}"--}}
                                                >
                                            </a>
                                        @else
                                            <img loading="lazy" src="{{ $block['data']['file']['url'] }}"
                                                 class="figure-img img-fluid rounded"
                                                 width="825"
                                                {{--                         alt="{% if altExists %}{{ block.data.caption|raw|split('|')|last }}{% endif %}"--}}
                                            >
                                        @endif

                                        <figcaption class="figure-caption text-center">
                                            {{ $block['data']['caption'] }}
                                        </figcaption>
                                    </figure>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <!--          Каталог сортировка акционные товары-->
{{--                    <a href="{{ route('') }}" class="promotion-detail__link btn btn-medium">Смотреть товары</a>--}}
                </div>
            </div>
        </div>
    </section>
@endsection
