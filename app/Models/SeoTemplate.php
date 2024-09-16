<?php

namespace App\Models;

use App\Models\Shop\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoTemplate extends Model
{
    protected $table = 'seo_template';

    const MATERIAL_TYPE_PRODUCT = 'product';
    const MATERIAL_TYPE_CATEGORY = 'category';

    const TYPE_TEMPLATE_TITLE = 'title';
    const TYPE_TEMPLATE_DESCRIPTION = 'description';

    const LIST_MATERIAL = [
        self::MATERIAL_TYPE_PRODUCT => 'Продукт',
        self::MATERIAL_TYPE_CATEGORY => 'Категория',
    ];

    protected $fillable = [
        'name',
        'category_id',
        'is_main',
        'text_template',
        'type_material',
        'type_template'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getTypeMaterial(): string
    {
        if (isset($this->type_material)) {
            return self::LIST_MATERIAL[$this->type_material];
        }
        return '';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

}
