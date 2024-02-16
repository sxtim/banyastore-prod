<?php

namespace App\Models\Shop;

use App\Http\Filters\Filterable;
//use App\Http\Filters\ProductFilter;


use App\Models\Shop\Property\PropertyValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;


class Product extends Model
{
    use Filterable, HasSlug;

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
        'price' => 'float'
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

}
