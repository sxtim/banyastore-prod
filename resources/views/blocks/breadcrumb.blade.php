<section class="breadcrumb">
    <li class="container">
        <nav class="breadcrumb__nav" aria-label="breadcrumb">
            <ol class="breadcrumb__list">
                @foreach($breadcrumbs as $breadcrumb)
                    @if ($breadcrumb->url && !$loop->last)
                        <li >
                            <a class="breadcrumb__item" href="{{ $breadcrumb->url }}">
                                {{ $breadcrumb->title }}
                            </a>
                        </li>
                    @else
                        <li >
                            <a class="breadcrumb__item breadcrumb-active" href="{{ $breadcrumb->url }}">
                                {{ $breadcrumb->title }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
</section>
