@extends('layouts.backend')

@section('pagetitle', 'Редактировать скидку')
@section('aside_discounts', 'active')

@section('content')
    <a href="{{ route('backend.discount.index') }}" class="back-link">
        Скидки
    </a>

    <div class="pagetitle">
        Редактировать скидку
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
          action="{{ route('backend.discount.update', ['discountId' => $discount->id]) }}"
          method="post" enctype="multipart/form-data"
    >
        @method('PATCH')
        @include('backend.discount.form')
    </form>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        //подключен select2
        $('#discount-products').select2({
            placeholder: "Выберите продукты",
            language: "ru",
        });

    </script>
@endsection
