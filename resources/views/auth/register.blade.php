@extends('layouts.app')

@section('pagetitle', 'Регистрация | Banyastore')

@section('content')
    <section class="registration section">
        <div class="container">
            <div class="center-title-container">
                <h1 class="center-title title-s">РЕГИСТРАЦИЯ</h1>
            </div>
            <div class="reg__form-wrapper" id="register-form">
                <register-component></register-component>
            </div>
        </div>
    </section>
@endsection
