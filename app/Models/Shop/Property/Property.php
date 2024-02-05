<?php
namespace App\Models\Shop\Property;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Property extends Model
{
    use HasSlug;

    protected $table = 'properties';

    protected $fillable = [
        'name',
        'slug',
        'is_required',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(PropertyValue::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->usingLanguage('en');
    }
}
