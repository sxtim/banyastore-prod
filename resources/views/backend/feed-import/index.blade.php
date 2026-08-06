@extends('layouts.backend')

@section('pagetitle', 'Импорт товаров')
@section('aside_shop', 'active')

@section('content')
    <a href="{{ route('backend.shop.index') }}" class="back-link">Интернет-магазин</a>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="pagetitle mb-0">Импорт товаров</div>
        <a href="{{ $source->url }}" target="_blank" rel="noopener noreferrer">{{ $source->name }}</a>
    </div>

    <details class="feed-import-help mb-3">
        <summary>Как работать с импортом</summary>
        <div class="pt-3 pb-1">
            <p>
                Обычный порядок: проверить фид и запустить импорт.
            </p>
            <ol class="pl-4 mb-3">
                <li><strong>Проверить фид</strong> — скачать свежие данные и подготовить отчёт. Товары сайта при этом не меняются.</li>
                <li>Если во вкладке <strong>Требуют решения</strong> стоит ноль, ничего дополнительно делать не нужно.</li>
                <li><strong>Запустить импорт</strong> — применить подготовленные изменения. Существующие товары обновятся, новые будут созданы.</li>
                <li><strong>История</strong> — посмотреть результаты, скачать CSV-отчёт или кнопкой «Откатить» отменить последний импорт.</li>
            </ol>
            <p>
                У существующих товаров обновляются цена, скидка из фида, свойства и фотографии;
                наши название и категория сохраняются. Если в фиде есть старая цена, она показывается
                зачёркнутой, а текущая цена товара совпадает с ценой фида.
                Товары из исключённых категорий и товары, пропавшие из фида, не изменяются.
                Для характеристик используются структурированные поля фида; описание только дополняет недостающие данные.
            </p>
        </div>
    </details>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    @if(
        $latestPreviewAttempt?->status === \App\Models\Feed\FeedImportRun::STATUS_FAILED
        && $latestPreviewAttempt?->id !== $latestPreview?->id
    )
        <div class="alert alert-danger">
            Проверка #{{ $latestPreviewAttempt->id }} не выполнена:
            {{ $latestPreviewAttempt->error }}
            @if($latestPreview)
                <div class="mt-1">Ниже сохранён последний успешный отчёт #{{ $latestPreview->id }}.</div>
            @endif
        </div>
    @endif

    <div class="border-top border-bottom py-2 mb-4 text-muted">
        <div>
            Товары сайта обновлены:
            <strong>{{ $latestSuccessfulImport?->finished_at?->format('d.m.Y H:i') ?? 'ещё не обновлялись' }}</strong>.
        </div>
        <div>
            Последняя проверка: {{ $latestPreview?->finished_at?->format('d.m.Y H:i') ?? 'не выполнялась' }}.
            Фид поставщика сформирован:
            {{ $latestPreview?->feed_generated_at?->format('d.m.Y H:i') ?? 'дата не указана' }}.
        </div>
    </div>

    @if ($activeRun)
        @php($isPreviewRun = $activeRun->kind === \App\Models\Feed\FeedImportRun::KIND_PREVIEW)
        <div class="alert alert-info mb-4" id="feed-run-progress"
             data-status-url="{{ route('backend.feed-import.status', $activeRun) }}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>
                    <span data-operation-label>{{ $activeRun->kind_label }}</span>
                    #{{ $activeRun->id }}:
                    <span data-status-label>{{ $activeRun->status_label }}</span>
                </strong>
                <span data-progress-label>0%</span>
            </div>
            <div class="progress">
                <div class="progress-bar {{ $isPreviewRun ? 'progress-bar-striped progress-bar-animated' : '' }}"
                     role="progressbar" style="width: {{ $isPreviewRun ? 100 : 0 }}%"
                     aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"
                     data-progress-bar></div>
            </div>
            <div class="small mt-2" data-progress-count>
                {{ $isPreviewRun ? 'Скачиваем и анализируем фид...' : 'Получаем состояние операции...' }}
            </div>
        </div>
    @endif

    <ul class="nav nav-tabs feed-import-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'run' ? 'active' : '' }}"
               href="{{ route('backend.feed-import.index') }}">Запуск</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'review' ? 'active' : '' }}"
               href="{{ route('backend.feed-import.index', ['tab' => 'review']) }}">
                Требуют решения
                @if($pendingItems->count()) <span class="badge badge-warning">{{ $pendingItems->count() }}</span> @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'history' ? 'active' : '' }}"
               href="{{ route('backend.feed-import.index', ['tab' => 'history']) }}">История</a>
        </li>
    </ul>

    @if ($tab === 'run')
        @if (!$activeRun)
            <form action="{{ route('backend.feed-import.preview') }}" method="post" class="mb-4">
                @csrf
                <button type="submit" class="btn btn-primary"
                        data-feed-submit data-loading-text="Проверяем фид...">Проверить фид</button>
            </form>
        @endif

        @if ($completedImportForLatestPreview)
            @php($importSummary = $completedImportForLatestPreview->summary ?? [])
            @php($importHasErrors = ($importSummary['error'] ?? 0) > 0)
            <div class="alert alert-{{ $importHasErrors ? 'warning' : 'success' }}">
                <strong>
                    Импорт #{{ $completedImportForLatestPreview->id }}
                    {{ $importHasErrors ? 'завершён с ошибками' : 'успешно завершён' }}
                    {{ $completedImportForLatestPreview->finished_at?->format('d.m.Y H:i') }}.
                </strong>
                <div class="mt-1">
                    Обработано: {{ $importSummary['success'] ?? 0 }},
                    пропущено: {{ $importSummary['skipped'] ?? 0 }},
                    ошибок: {{ $importSummary['error'] ?? 0 }}.
                </div>
                <a href="{{ route('backend.feed-import.index', ['tab' => 'history']) }}">
                    Открыть историю
                </a>
            </div>
        @elseif ($latestPreview)
            @php($summary = $latestPreview->summary ?? [])
            <div class="row mb-4">
                @foreach([
                    'update' => ['Связанные товары', 'primary'],
                    'create' => ['Создать', 'success'],
                    'pending' => ['Требуют решения', 'warning'],
                    'errors' => ['Ошибки', 'danger'],
                ] as $key => [$label, $color])
                    <div class="col-md-3 col-6 mb-2">
                        <div class="border-left border-{{ $color }} pl-3 py-2">
                            <div class="small text-muted">{{ $label }}</div>
                            <strong class="h4">{{ $summary[$key] ?? 0 }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(($summary['excluded'] ?? 0) > 0 || ($summary['removed'] ?? 0) > 0)
                <p class="text-muted mb-3">
                    Без изменений:
                    {{ $summary['excluded'] ?? 0 }} исключено,
                    {{ $summary['removed'] ?? 0 }} отсутствует в текущем фиде.
                </p>
            @endif

            <div class="d-flex align-items-center mb-3">
                @if(
                    !$activeRun
                    && $canApplyPreview
                    && $latestPreview->status === \App\Models\Feed\FeedImportRun::STATUS_READY
                )
                    <form action="{{ route('backend.feed-import.apply', $latestPreview) }}" method="post"
                          data-feed-operation-form
                          onsubmit="return confirm('Запустить импорт готовых товаров из проверки #{{ $latestPreview->id }}?')">
                        @csrf
                        <button type="submit" class="btn btn-success"
                                data-feed-submit data-loading-text="Запускаем импорт...">Запустить импорт</button>
                    </form>
                @endif
                <span class="ml-3 text-muted">Проверка #{{ $latestPreview->id }}, снимок {{ Str::limit($latestPreview->snapshot_hash, 12, '') }}</span>
            </div>

            <div class="mb-3">
                @foreach([
                    '' => 'Все',
                    'update' => 'Связанные товары',
                    'create' => 'Создать',
                ] as $action => $label)
                    <a href="{{ route('backend.feed-import.index', array_filter(['item_action' => $action])) }}"
                       class="btn btn-sm {{ $itemAction === $action ? 'btn-primary' : 'btn-outline-secondary' }} mr-1 mb-1">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @include('backend.feed-import.parts.items-table', ['items' => $items])
        @else
            <div class="alert alert-light border">Сначала выполните предварительную проверку фида.</div>
        @endif
    @elseif ($tab === 'review')
        @forelse($pendingItems as $item)
            @php($payload = $item->feed_payload ?? [])
            @php($candidates = $item->diff['candidates'] ?? [])
            <div class="border-bottom pb-4 mb-4 feed-review-item" data-item-id="{{ $item->id }}">
                <div class="row">
                    <div class="col-md-2">
                        @if(!empty($payload['pictures'][0]))
                            <img src="{{ $payload['pictures'][0] }}" alt="" class="img-fluid"
                                 style="max-height: 140px; object-fit: contain;">
                        @endif
                    </div>
                    <div class="col-md-10">
                        <div class="d-flex justify-content-between">
                            <h5>{{ $payload['name'] ?? $item->offer_id }}</h5>
                            <span class="text-muted">offer id {{ $item->offer_id }}</span>
                        </div>
                        <div>{{ number_format($payload['price'] ?? 0, 0, ',', ' ') }} руб. · {{ $payload['category_name'] ?? '' }}</div>

                        @if($candidates)
                            <div class="mt-3"><strong>Возможные совпадения</strong></div>
                            @foreach($candidates as $candidate)
                                <form action="{{ route('backend.feed-import.decision', $item) }}" method="post"
                                      class="d-flex align-items-center border-top py-2">
                                    @csrf
                                    <input type="hidden" name="decision" value="link">
                                    <input type="hidden" name="product_id" value="{{ $candidate['id'] }}">
                                    <div class="flex-grow-1">
                                        ID {{ $candidate['id'] }} · {{ $candidate['name'] }}
                                        · {{ number_format($candidate['price'], 0, ',', ' ') }} руб.
                                        · совпадение {{ $candidate['score'] }}%
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Связать</button>
                                </form>
                            @endforeach
                        @endif

                        <div class="mt-3">
                            <label>Найти другой товар сайта</label>
                            <input type="search" class="form-control feed-product-search"
                                   placeholder="Введите часть названия">
                            <select class="form-control mt-2 feed-product-results" size="4" hidden></select>

                            <div class="form-row justify-content-end mt-2">
                                <div class="col-md-3">
                                    <form action="{{ route('backend.feed-import.decision', $item) }}" method="post"
                                          class="feed-link-form">
                                        @csrf
                                        <input type="hidden" name="decision" value="link">
                                        <input type="hidden" name="product_id" class="feed-selected-product">
                                        <button type="submit" class="btn btn-outline-primary btn-block" disabled>
                                            Связать выбранный
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-2">
                                    <form action="{{ route('backend.feed-import.decision', $item) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="decision" value="create">
                                        <button type="submit" class="btn btn-success btn-block">Создать новый</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-success">Все товары текущей проверки имеют решение.</div>
        @endforelse
    @else
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Операция</th>
                <th>Статус</th>
                <th>Дата фида</th>
                <th>Начало</th>
                <th>Результат</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($runs as $run)
                <tr>
                    <td>{{ $run->id }}</td>
                    <td>{{ $run->kind_label }}</td>
                    <td>{{ $run->status_label }}</td>
                    <td>{{ $run->feed_generated_at?->format('d.m.Y H:i') }}</td>
                    <td>{{ $run->started_at?->format('d.m.Y H:i') }}</td>
                    <td>
                        @foreach(($run->summary ?? []) as $key => $value)
                            <span class="mr-2">{{ [
                                'total' => 'всего',
                                'create' => 'создать',
                                'update' => 'обновить',
                                'pending' => 'требуют решения',
                                'removed' => 'нет в текущем фиде',
                                'excluded' => 'исключено',
                                'error' => 'ошибки',
                                'errors' => 'ошибки',
                                'success' => 'успешно',
                                'warning' => 'предупреждения',
                                'skipped' => 'пропущено',
                            ][$key] ?? $key }}: {{ $value }}</span>
                        @endforeach
                        @if($run->error)<div class="text-danger">{{ $run->error }}</div>@endif
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('backend.feed-import.report', $run) }}">CSV</a>
                        @if(
                            $run->kind === \App\Models\Feed\FeedImportRun::KIND_APPLY
                            && ($run->summary['error'] ?? 0) > 0
                            && !$activeRun
                        )
                            <form action="{{ route('backend.feed-import.retry-errors', $run) }}" method="post"
                                  class="d-inline ml-2"
                                  data-feed-operation-form
                                  onsubmit="return confirm('Повторить только ошибочные товары импорта #{{ $run->id }}?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary"
                                        data-feed-submit data-loading-text="Запускаем...">Повторить ошибки</button>
                            </form>
                        @endif
                        @if($rollbackableRun?->id === $run->id && !$activeRun)
                            <form action="{{ route('backend.feed-import.rollback', $run) }}" method="post"
                                  class="d-inline ml-2"
                                  data-feed-operation-form
                                  onsubmit="return confirm('Откатить последний импорт #{{ $run->id }}?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        data-feed-submit data-loading-text="Запускаем откат...">Откатить</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection

@section('scripts')
    <script>
        const progress = document.getElementById('feed-run-progress');
        if (progress) {
            let pollTimer;

            const poll = async () => {
                try {
                    const response = await fetch(progress.dataset.statusUrl, {
                        headers: {'Accept': 'application/json'},
                        cache: 'no-store',
                    });
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);

                    const data = await response.json();
                    const progressBar = progress.querySelector('[data-progress-bar]');
                    const indeterminate = data.kind === 'preview' && !data.finished && data.total === 0;
                    progressBar.classList.toggle('progress-bar-striped', indeterminate);
                    progressBar.classList.toggle('progress-bar-animated', indeterminate);
                    progressBar.style.width = indeterminate ? '100%' : `${data.progress}%`;
                    progressBar.setAttribute('aria-valuenow', indeterminate ? 0 : data.progress);
                    progress.querySelector('[data-progress-label]').textContent =
                        indeterminate ? 'выполняется' : `${data.progress}%`;
                    progress.querySelector('[data-operation-label]').textContent = data.kind_label;
                    progress.querySelector('[data-status-label]').textContent = data.status_label;
                    progress.querySelector('[data-progress-count]').textContent =
                        indeterminate
                            ? 'Скачиваем и анализируем фид...'
                            : data.total > 0
                                ? `Обработано ${data.processed} из ${data.total}`
                                : data.error || data.status_label;

                    if (data.finished) {
                        progress.classList.remove('alert-info');
                        progress.classList.add(
                            data.status === 'failed' ? 'alert-danger'
                                : data.status === 'completed_with_errors' ? 'alert-warning'
                                    : 'alert-success'
                        );
                        if (data.error) {
                            progress.querySelector('[data-progress-count]').textContent = data.error;
                        }
                        window.setTimeout(() => window.location.reload(), 800);
                        return;
                    }

                    pollTimer = window.setTimeout(poll, 1500);
                } catch (error) {
                    progress.querySelector('[data-progress-count]').textContent =
                        'Не удалось обновить состояние. Повторяем...';
                    pollTimer = window.setTimeout(poll, 3000);
                }
            };

            poll();

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    window.clearTimeout(pollTimer);
                    poll();
                }
            });
        }

        document.querySelectorAll('[data-feed-submit]').forEach((button) => {
            button.form?.addEventListener('submit', (event) => {
                if (event.defaultPrevented) return;

                button.disabled = true;
                button.textContent = button.dataset.loadingText;
            });
        });

        document.querySelectorAll('.feed-review-item').forEach((row) => {
            const search = row.querySelector('.feed-product-search');
            const results = row.querySelector('.feed-product-results');
            const form = row.querySelector('.feed-link-form');
            if (!search || !results || !form) return;
            let timer;

            search.addEventListener('input', () => {
                window.clearTimeout(timer);
                if (search.value.trim().length < 2) {
                    results.hidden = true;
                    return;
                }
                timer = window.setTimeout(async () => {
                    const url = new URL(@json(route('backend.feed-import.product-search')), window.location.origin);
                    url.searchParams.set('q', search.value.trim());
                    const response = await fetch(url, {headers: {'Accept': 'application/json'}});
                    const products = await response.json();
                    results.innerHTML = products.map((product) =>
                        `<option value="${product.id}">ID ${product.id} · ${product.name} · ${product.price} руб.</option>`
                    ).join('');
                    results.hidden = false;
                }, 300);
            });

            results.addEventListener('change', () => {
                form.querySelector('.feed-selected-product').value = results.value;
                form.querySelector('button').disabled = !results.value;
            });
        });
    </script>
@endsection
