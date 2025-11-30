<?php

namespace App\Models\Shop;

use App\Http\Filters\Filterable;
//use App\Http\Filters\ProductFilter;


use App\Models\Shop\ProductFile;
use App\Models\Shop\Property\PropertyValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;


/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $category_id
 * @property float $price
 * @property int $is_active
 * @property int $sort
 * @property string $image
 * @property array $description
 * @property array|null $preview_text
 * @property string|null $tag
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $discount_id
 * @property-read \App\Models\Shop\Category $category
 * @property-read \App\Models\Shop\Discount|null $discount
 * @property-read string $image_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PropertyValue> $propertiesValues
 * @property-read int|null $properties_values_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product filter(\App\Http\Filters\QueryFilter $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePreviewText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    use Filterable, HasSlug;

    const HIT_TAG = 'hit';
    const NEW_TAG = 'new';
    const ACTION_TAG = 'action';

    const LIST_TAG = [
        self::HIT_TAG => 'Хит',
        self::NEW_TAG => 'Новинка',
        self::ACTION_TAG => 'Акция',
    ];

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'quantity',
        'price',
        'image',
        'is_active',
        'sort',
        'slug',
        'discount_id',
        'preview_text',
        'tag',
        'is_popular'
    ];

    protected $hidden = [
        'image',
        'category_id'
    ];

    protected $appends = [
        'image_url'
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

    //дополнительные картинки к товару
    public function additionalImages(): HasMany
    {
        return $this
            ->hasMany(ProductFile::class, 'product_id')
            ->where('type', ProductFile::TYPE_IMAGE);
    }

    public function favorite(int $userId): Product
    {
        if ($this->favorites->where('user_id', $userId)->isNotEmpty()) {

            $this->favorites()->where('user_id', $userId)->delete();

            return $this;
        }

        $this->favorites()->create([
            'user_id' => $userId
        ]);

        return $this;
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(ProductFavorite::class, 'product_id');
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_related_products',
            'product_id',
            'related_product_id'
        );
    }

    public function boughtTogether(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_bought_together',
            'product_id',
            'related_product_id'
        );
    }
}
