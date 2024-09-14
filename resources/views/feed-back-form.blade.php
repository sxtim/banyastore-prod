@extends('layouts.app')

@section('pagetitle', 'Обратная связь | Banyastore')

@section('content')
    <div class="container">
        <section class="feedback section">
            <div class="center-title-container">
                <h1 class="center-title title-s">Заказать звонок</h1>
            </div>
            <div id="feedback-form">
                <feedback-page></feedback-page>
            </div>
        </section>
    </div>
@endsection
