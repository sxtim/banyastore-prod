@extends('layouts.backend')

@section('pagetitle', 'Добавить баннер')
@section('aside_banner', 'active')

@section('content')

    <a href="{{ route('backend.banner.index') }}" class="back-link">
        Баннеры
    </a>

    <div class="pagetitle">
        Добавить баннер
    </div>

    @if (\Session::has('success'))
        <div class="alert alert-success">
            {!! \Session::get('success') !!}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('backend.banner.store') }}" method="post" enctype="multipart/form-data">
        @include('backend.banner.form')
    </form>

@endsection

