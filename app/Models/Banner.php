<?php

namespace App\Models;

use App\Services\BannerImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'name',
        'sort',
        'link',
        'image',
        'is_active'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    public function getUrlImage(): ?string
    {
        return Storage::url($this->image);
    }

    public function getUrlImageMobileWebp(): ?string
    {
        return app(BannerImageService::class)->getVariantUrl($this->image, BannerImageService::VARIANT_MOBILE);
    }

    public function getUrlImageDesktopWebp(): ?string
    {
        return app(BannerImageService::class)->getVariantUrl($this->image, BannerImageService::VARIANT_DESKTOP);
    }

}
