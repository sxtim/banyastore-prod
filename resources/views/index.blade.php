@extends('layouts.app')

@section('pagetitle', 'Главная | Banyastore')

@section('content')
    @include('blocks/section-top')
    @include('blocks/section-benefits')
    @include('blocks/section-brands')
    @include('blocks/section-popular-goods')
    @include('blocks/section-3d-projects')
    @include('blocks/section-news-main')
@endsection
