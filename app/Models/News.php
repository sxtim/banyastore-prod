<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class News extends Model
{
    use HasSlug;


    protected $fillable = [
        'name',
        'slug',
        'sort',
        'preview_img',
        'main_img',
        'preview_text',
        'detail_text',
        'is_active',
        'btn',
        'link_btn',
        'start_at',
        'end_at'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'detail_text' => 'array'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->usingLanguage('en');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getPreviewImg(): string
    {
        return Storage::url($this->preview_img);
    }

    public function getMainImage(): ?string
    {
        return Storage::url($this->main_img);
    }

}
