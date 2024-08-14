@extends('layouts.app')

@section('pagetitle', 'Акции | Banyastore')

@section('content')
    {{ Breadcrumbs::render('actions') }}

    <section class="promotions-page section">
        <div class="container">
            <h1 class="promotions-page__title title-s">Акции интернет-магазина Banyastore</h1>

            <div class="cards-wrapper promotions-page__cards-wrapper">
                @foreach($actions as $action)
                    <article class="promotions-card">
                        <a href="{{ route('actions.detail', ['slug' => $action->slug]) }}"
                           class="promotions-card__link"
                        >
                            <div class="promotions-card__picture">
                                <img src="{{ $action->getPreviewImage() }}" alt="{{ $action->name }}">
                            </div>
                            <div class="promotions-card__desc">
                                <h2 class="promotions-card__title">
                                    {{ $action->name }}
                                </h2>
                                @if($action->start_at && $action->end_at)
                                    <time class="promotions-card__date">
                                        {{ $action->start_at->format('d.m.y') }}
                                        - {{ $action->end_at->format('d.m.y') }}
                                    </time>
                                @endif
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
