@extends('layouts.app')

@section('pagetitle', '3D проекты | Banyastore')

@section('content')
    <div class="projects-page">
        @@include('blocks/breadcrumb.html')
        <div class="projects-page__top">
            <div class="container">
                <h1 class="title title-s projects-page__top-title">3D проектирование парного помещения</h1>
            </div>

            <img class="projects-page__top-picture" src="/images/3d-projects/3d-projects-page/3d-01.jpg" alt="picture">
        </div>
        <section class="projects-page__content">
            <div class="projects-page__content-top">
                <div class="container">
                    <div class="projects-page__content-top">
                        <p>Вы постоянно заботитесь о своем здоровье, а также о здоровье своей семьи и детей? Уверяем
                            Вас, что нет ничего лучше, чем иметь собственную сауну или баню. Это идеальный способ
                            поддерживать ваше самочувствие. Еще в древности народы верили, что баня имеет исцеляющие
                            свойства и способна излечить любое заболевание. Современная наука подтверждает правоту этих
                            древних убеждений.</p>
                        <br>
                        <p>Если вы уже приняли решение о строительстве собственной сауны или бани, то компания
                            Banyastore окажет вам профессиональную помощь в решении этого сложного вопроса.</p>
                    </div>
                </div>
            </div>
            <div class="projects-page__content-container">
                <div class="projects-page__content-wrapper">
                    <div class="projects-page__content-left">
                        <div class="projects-page__content-left-t">
                            <p>
                                Опытные проектировщики компании Banyastore предоставят вам услуги по 3D-проектированию
                                парных
                                преимущественно в Москве и Московской области,а также дистанционно по всей России.
                            </p>
                            <br>
                            <p>
                                Наша компания предлагает вам выполнение прекрасно разработанного 3D-проекта вашей
                                будущей сауны или
                                бани,
                                будь то частное или общественное сооружение, русская или турецкая баня, финская или
                                инфракрасная сауна,
                                баня
                                из бруса или бревна.
                            </p>
                            <br>
                            <p>
                                С помощью объемного изображения вы сможете наглядно представить себе будущий объект с
                                сохранением
                                естественных цветов.
                            </p>
                        </div>
                        <div class="projects-page__content-left-b">
                            <p>
                                Наши специалисты помогут вам определиться со всеми мелочами в строительстве бани или
                                сауны. Вам нужно
                                будет ясно представить себе размер и вместительность вашей будущей парной, выбрать тип
                                объекта
                                (отдельное здание или пристройка) и дополнительные помещения, которые вы хотели бы
                                включить в этот
                                проект.
                            </p>
                        </div>
                    </div>
                    <div class="projects-page__content-right">
                        <div class="projects-page__content-right-list-cont">
                            <ul class="projects-page__content-right-list">
                                <li>Banyastore предлагает свою экспертную помощь в лице опытных
                                    инженеров-проектировщиков, которые
                                    помогут вам:
                                </li>
                                <br>
                                <br>
                                <li> &#9989 разработать весь комплекс проектной документации точно и грамотно;</li>
                                <br>
                                <li> &#9989 тщательно рассчитать все требующееся оборудование;</li>
                                <br>
                                <li> &#9989 продумать и посоветовать оптимальное месторасположение объекта;</li>
                                <br>
                                <li> &#9989 подобрать самые современные варианты экстерьера и интерьера;</li>
                                <br>
                                <li> &#9989 обеспечить устройству функциональность и безопасность.</li>
                            </ul>
                        </div>
                        <img class="projects-page__content-picture" src="/images/3d-projects/3d-projects-page/3d-02.jpg"
                             alt="picture">
                    </div>
                </div>
            </div>
        </section>
        <section class="projects-page__carousel">
            <div class="container">
                <h2 class="projects-page__carousel-title title">Наши проекты 3D</h2>
                <div class="projects-page__slider-container">
                    <div id="projects-page__slider" class="keen-slider">
                        @@loop('./blocks/card-project-slide.html','./data/card-project.json')
                    </div>
                </div>
            </div>
        </section>
        <section class="projects-page__bottom">
            <div class="projects-page__bottom-wrapper">
                <img class="projects-page__bottom-img" src="/images/3d-projects/3d-projects-page/3d-03.jpg">
                <div class="projects-page__bottom-txt">
                    <p>Banyastore разработает для Вас подробный пакет проектной документации и предоставит возможность просмотра
                        3D-изображения проекта.</p>
                    <br>
                    <p>Позвоните нам прямо сейчас, задайте все интересующие вопросы, и мы с удовольствием на них ответим.</p>
                    <br>
                    <p>Banyastore - ваш партнер в 3D-проектировании парных помещений "под ключ".</p>
                    <button class="projects-page__bottom-btn btn btn-white">Заказать 3D проект</button>
                </div>
            </div>
        </section>
    </div>
@endsection
