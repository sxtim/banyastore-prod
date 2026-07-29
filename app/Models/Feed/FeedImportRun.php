<?php

namespace App\Models\Feed;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedImportRun extends Model
{
    public const KIND_PREVIEW = 'preview';

    public const KIND_APPLY = 'apply';

    public const KIND_ROLLBACK = 'rollback';

    public const STATUS_PREPARING = 'preparing';

    public const STATUS_READY = 'ready';

    public const STATUS_RUNNING = 'running';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_COMPLETED_WITH_ERRORS = 'completed_with_errors';

    public const STATUS_FAILED = 'failed';

    public const STATUS_ROLLED_BACK = 'rolled_back';

    protected $fillable = [
        'feed_source_id',
        'user_id',
        'parent_run_id',
        'kind',
        'status',
        'batch_id',
        'snapshot_path',
        'snapshot_hash',
        'feed_generated_at',
        'summary',
        'error',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'feed_generated_at' => 'datetime',
        'summary' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(FeedSource::class, 'feed_source_id');
    }

    public function parentRun(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_run_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FeedImportItem::class);
    }

    public function getKindLabelAttribute(): string
    {
        return match ($this->kind) {
            self::KIND_PREVIEW => 'Проверка',
            self::KIND_APPLY => 'Импорт',
            self::KIND_ROLLBACK => 'Откат',
            default => (string) $this->kind,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PREPARING => 'Подготовка',
            self::STATUS_READY => 'Готово',
            self::STATUS_RUNNING => 'Выполняется',
            self::STATUS_COMPLETED => 'Завершено',
            self::STATUS_COMPLETED_WITH_ERRORS => 'Завершено с ошибками',
            self::STATUS_FAILED => 'Ошибка',
            self::STATUS_ROLLED_BACK => 'Откачено',
            default => (string) $this->status,
        };
    }
}
