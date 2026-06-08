@extends('layouts.app')

@section('pagetitle', 'Банястор - эксперты в печной индустрии')
@section('description', 'Интернет-магазин    товаров для бани оптом и в розницу в Домодедово')

@section('head')
    @if($banners->first())
        @if($banners->first()->getUrlImageMobileWebp())
            <link rel="preload" as="image" href="{{ $banners->first()->getUrlImageMobileWebp() }}" media="(max-width: 767px)" fetchpriority="high">
        @endif
        @if($banners->first()->getUrlImageDesktopWebp())
            <link rel="preload" as="image" href="{{ $banners->first()->getUrlImageDesktopWebp() }}" media="(min-width: 768px)" fetchpriority="high">
        @else
            <link rel="preload" as="image" href="{{ $banners->first()->getUrlImage() }}" fetchpriority="high">
        @endif
    @endif
@endsection

@section('content')
    @include('blocks/section-top')
    @include('blocks/section-benefits')
    @include('blocks/section-brands')
    @include('blocks/section-popular-goods')
    @include('blocks/section-3d-projects')
    @include('blocks/section-news-main')
@endsection
