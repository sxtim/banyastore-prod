@extends('layouts.app')

@section('pagetitle', 'О компании | Banyastore')

@section('content')
    <section class="about-company__content section">
        <div class="container">
            <h1 class="about-company__title title title-s">О Компании</h1>
            <div class="about-company__content-txt">
                <p>Компания «Ферингер Опт» работает с 2010, у нас имеется оптово-розничный магазин и большой склад в г
                    Домодедово. В 2024 мы решили, что наши товары должен оценить каждый любитель бани и открыли интернет-магазин
                    banyastore. ru
                </p>
                <br>
                <p>
                    «Банные истории»- это магазин где любят русскую баню и знают все о ее традициях. Мы уверены, что самое ценное
                    в семейной бане — это качественный отдых, оздоровление, общение с близкими, а это значит, что оборудование в
                    бане должно быть простым и надежным.
                </p>
                <br>
                <p>
                    Поэтому в своём магазине у нас только качественный и проверенный ассортимент, удобные в использовании, но при
                    этом эффективные и красивые банные печи, специальные дымоходы, современные аксессуары и многое другое.
                </p>
                <br>
                Мы делаем все, чтобы Вы наслаждались приятной компанией и парением с ароматным веником
            </div>
        </div>
    </section>
    <section class="about-company__rekvizity section">
        <div class="container">
            <h3 class="title title-s about-company__rekvizity-title">Реквизиты</h3>
            <table class="about-company__rekvizity-table" style="max-width:1000px">
                <tbody>
                <tr>
                    <th scope="row">Полное наименование</th>
                    <td>Индивидуальный предприниматель Шевцова Нина Александровна</td>
                </tr>
                <tr>
                    <th scope="row">ИНН</th>
                    <td>500913301643</td>
                </tr>
                <tr>
                    <th scope="row">ОГРНИП</th>
                    <td>319502700111629</td>
                </tr>
                <tr>
                    <th scope="row">Юридический адрес/Фактический адрес</th>
                    <td>г. Домодедово,
                        Белые Столбы,
                        ул Авенариуса, стр 6</td>
                </tr>
                <tr>
                    <th scope="row">Телефон</th>
                    <td>+7 (499) 714 71 44 &nbsp; / &nbsp; +7 (977) 144 77 36</td>
                </tr>
                <tr>
                    <th scope="row">Расчетный счет</th>
                    <td>40802810002090002885</td>
                </tr>
                <tr>
                    <th scope="row">Банк</th>
                    <td>АО "Альфа-банк"</td>
                </tr>
                <tr>
                    <th scope="row">Корр счет банка</th>
                    <td>30101810200000000593</td>
                </tr>
                <tr>
                    <th scope="row">БИК</th>
                    <td>044525593</td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection
