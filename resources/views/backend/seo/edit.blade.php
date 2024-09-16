@extends('layouts.backend')

@section('pagetitle', 'Редактировать шаблон')
@section('aside_seo', 'active')

@section('content')
    <a href="{{ route('backend.seo.index') }}" class="back-link">
        Шаблоны
    </a>

    <div class="pagetitle">
        Редактировать шаблон
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

    <form class="form-wrap-modif"
          action="{{ route('backend.seo.update', ['templateId' => $seoTemplate->id]) }}"
          method="post" enctype="multipart/form-data"
          id="seo_form"
    >
        @method('PATCH')
        @include('backend.seo.form')
    </form>
@endsection

@section('scripts')
@endsection
