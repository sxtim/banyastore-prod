@extends('layouts.app')

@section('pagetitle', 'Новости | Banyastore')

@section('content')
    {{ Breadcrumbs::render('news') }}
    @include('blocks/section-news')
@endsection
