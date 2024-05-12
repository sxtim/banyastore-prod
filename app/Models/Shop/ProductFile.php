<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;


class ProductFile extends Model
{
    use HasFactory;

    const TYPE_IMAGE = 'image'; //картинка

    protected $table = 'product_files';

    protected $fillable = [
        'file_path',
        'type'
    ];

    protected $appends = [
        'image_url',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }
}
