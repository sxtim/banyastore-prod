<?php

namespace App\Models\Shop;

use App\Http\Filters\Filterable;
//use App\Http\Filters\ProductFilter;


use App\Models\Shop\Property\PropertyValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;


class Product extends Model
{
    use Filterable, HasSlug;

    const HIT_TAG = 'hit';
    const NEW_TAG = 'new';

    const LIST_TAG = [
        self::HIT_TAG => 'Хит',
        self::NEW_TAG => 'Новинка',
    ];

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'is_active',
        'sort',
        'slug',
        'discount_id',
        'preview_text',
        'tag'
    ];

    protected $hidden = [
        'image',
        'category_id'
    ];

    protected $appends = [
        'image_url',
        'pdf_url'
    ];

    protected $casts = [
        'price' => 'float',
        'description' => 'array',
        'preview_text' => 'array'
    ];


    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->usingLanguage('en');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function propertiesValues(): BelongsToMany
    {
        return $this->belongsToMany(PropertyValue::class, 'products_property_values', 'product_id', 'property_value_id')
            ->with(['property']);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class)->where('is_active', true);
    }


    public function getCurrentPrice(): float
    {
        if ($this->discount) {
            if ($this->discount->type === Discount::TYPE_RUB && $this->discount->discount < $this->price) {
                return $this->price - $this->discount->discount;
            } elseif ($this->discount->type === Discount::TYPE_PERCENT && $this->discount->discount < 100) {
                $discount = round(($this->price / 100) * $this->discount->discount);
                return $this->price - $discount;
            }
        }

        return $this->price;
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->image);
    }

    public function getTag(): string
    {
        return $this->tag ? self::LIST_TAG[$this->tag] : '';
    }
}
