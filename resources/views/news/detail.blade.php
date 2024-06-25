@extends('layouts.app')

@section('pagetitle', $news->name.' | Banyastore')

@section('content')
    {{ Breadcrumbs::render('news-detail', $news) }}
    <section class="news-detail section">
        <div class="container">

            <h1 class="news-detail__title title-s">
                {{ $news->name }}
            </h1>
            <div class="news-detail__wrapper">
                <div class="news-detail__picture">
                    <img src="{{ $news->getMainImage() }}" alt="{{ $news->name }}">
                </div>
                <div class="news-detail__desc">
                    <div class="news-detail__txt">
                        @if ($news->detail_text && isset($news->detail_text['blocks']))
                            @foreach($news->detail_text['blocks'] as $block)
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
                    <time class="news-detail__date">
                        {{ $news->created_at->format('d.m.Y') }}
                    </time>
                    <a href="{{ route('news.index') }}" class="news-detail__link btn btn-medium">Все новости</a>
                </div>
            </div>
        </div>
    </section>
@endsection
