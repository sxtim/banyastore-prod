@extends('layouts.app')

@section('pagetitle', 'Личный кабинет | Banyastore')

@section('content')
    {{ Breadcrumbs::render('personal') }}
    <section class="login-section section">
        <div class="container">
            <div id="login-form">
                <update-password-component :token="'{{ $token }}'"></update-password-component>
            </div>
        </div>
    </section>
@endsection
