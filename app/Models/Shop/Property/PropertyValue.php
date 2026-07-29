<?php
namespace App\Models\Shop\Property;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyValue extends Model
{
    use SoftDeletes;

    protected $table = 'property_values';

    protected $fillable = [
        'name',
        'property_id',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
