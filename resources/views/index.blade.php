@extends('layouts.app')

@section('pagetitle', 'Банястор - эксперты в печной индустрии')
@section('description', 'Интернет-магазин    товаров для бани оптом и в розницу в Домодедово')

@section('head')
    @if($banners->first())
        <link rel="preload" as="image" href="{{ $banners->first()->getUrlImage() }}" fetchpriority="high">
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
