<?php
namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class TelegramChat extends Model
{
    protected $fillable = [
        'chat_id',
        'name',
        'username',
        'message'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
