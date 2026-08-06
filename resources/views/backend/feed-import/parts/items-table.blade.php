<div class="table-responsive">
    <table class="table table-bordered table-hover" style="font-size: 13px;">
        <thead>
        <tr>
            <th>Статус</th>
            <th>Товар фида</th>
            <th>Наш товар</th>
            <th>Цена</th>
            <th>Категория</th>
            <th>Фото</th>
            <th>Подробности</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            @php($payload = $item->feed_payload ?? [])
            @php($diff = $item->diff ?? [])
            <tr>
                <td>
                    {{ [
                        'update' => 'Синхронизировать',
                        'create' => 'Создать',
                        'pending' => 'Решить',
                        'excluded' => 'Исключено',
                        'removed' => 'Нет в текущем фиде',
                    ][$item->action] ?? $item->action }}
                    @if($item->error)<div class="text-danger">{{ $item->error }}</div>@endif
                </td>
                <td>
                    @if($item->action === 'removed')
                        <span class="text-muted">Название недоступно</span>
                    @elseif(!empty($payload['url']))
                        <a href="{{ $payload['url'] }}" target="_blank" rel="noopener noreferrer">
                            {{ $payload['name'] ?? $item->offer_id }}
                        </a>
                    @else
                        {{ $payload['name'] ?? $item->offer_id }}
                    @endif
                    <div class="text-muted">
                        @if(!empty($payload['vendor_code']))артикул {{ $payload['vendor_code'] }} · @endif
                        offer id {{ $item->offer_id }}
                    </div>
                    @if(!empty($payload['vendor']) || !empty($payload['group_id']))
                        <div class="text-muted">
                            @if(!empty($payload['vendor']))производитель {{ $payload['vendor'] }}@endif
                            @if(!empty($payload['vendor']) && !empty($payload['group_id'])) · @endif
                            @if(!empty($payload['group_id']))группа {{ $payload['group_id'] }}@endif
                        </div>
                    @endif
                </td>
                <td>
                    @if($item->product)
                        <a href="{{ route('backend.product.edit', $item->product) }}">
                            ID {{ $item->product->id }} · {{ $item->product->name }}
                        </a>
                    @elseif($item->action === 'create')
                        Новый товар
                    @elseif($item->action === 'removed' && $item->link?->decision === 'create')
                        <span class="text-muted">Не создавался</span>
                    @else
                        <span class="text-muted">Связи с товаром нет</span>
                    @endif
                </td>
                <td>
                    @if(isset($diff['product_price']))
                        <div>
                            Сейчас:
                            {{ number_format($diff['product_current_price'] ?? $diff['product_price'], 0, ',', ' ') }}
                            @if(
                                isset($diff['product_current_price'])
                                && (float) $diff['product_current_price'] !== (float) $diff['product_price']
                            )
                                <span class="text-muted">
                                    (обычная {{ number_format($diff['product_price'], 0, ',', ' ') }})
                                </span>
                            @endif
                        </div>
                    @endif
                    @if(isset($payload['price']))
                        <div>
                            Из фида: {{ number_format($payload['price'], 0, ',', ' ') }}
                            @if(!empty($payload['old_price']))
                                <span class="text-muted">
                                    (обычная {{ number_format($payload['old_price'], 0, ',', ' ') }})
                                </span>
                            @endif
                        </div>
                    @else
                        —
                    @endif
                </td>
                <td>
                    {{ $diff['feed_category'] ?? '—' }}
                    @if(!empty($diff['target_category']))
                        → {{ $diff['target_category'] }}
                    @endif
                </td>
                <td>
                    @if($item->action === 'removed')
                        —
                    @else
                        {{ $diff['product_photo_count'] ?? 0 }} → {{ $diff['feed_photo_count'] ?? 0 }}
                    @endif
                </td>
                <td>
                    <details>
                        <summary>Показать</summary>
                        <div class="mt-2">
                            @if(!empty($diff['feed_properties']))
                                <div><strong>Характеристики после импорта</strong></div>
                                @foreach($diff['feed_properties'] as $name => $value)
                                    <div>{{ $name }}: {{ $value }}</div>
                                @endforeach
                            @endif
                            @if(!empty($diff['description']))
                                <div class="mt-2"><strong>Описание:</strong> {{ $diff['description'] }}</div>
                            @endif
                            @if(!empty($diff['message']))
                                <div>{{ $diff['message'] }}</div>
                            @endif
                            @if(in_array($item->action, ['update', 'create'], true))
                                <form action="{{ route('backend.feed-import.decision', $item) }}" method="post"
                                      class="mt-3"
                                      onsubmit="return confirm('Перевести товар в список ручной проверки?')">
                                    @csrf
                                    <input type="hidden" name="decision" value="pending">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        Связан не с тем товаром
                                    </button>
                                </form>
                            @endif
                        </div>
                    </details>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if(method_exists($items, 'links'))
    <div class="mt-3">
        {!! $items->links('backend.pagination') !!}
    </div>
@endif
