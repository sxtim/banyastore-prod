<?php
namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasSlug;


    protected $fillable = [
        'name',
        'description',
        'is_active',
        'sort',
        'slug',
        'image',
        'parent_id'
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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->orderBy('sort');
    }

    public function childrens(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->with('childrens');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subcategory(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')->with(['subcategory']);
    }

}
