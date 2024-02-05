<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;


class Role extends Model
{
    use HasSlug;

    const ROLE_USER_ID = 1;

    const ROLE_ADMIN_ID = 2;

    const ROLE_PURVEYOR_ID = 24;

    protected $fillable = [
        'name',
        'slug'
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

    public function scopeNotHidden(Builder $query): Builder
    {
        return $query->where('hidden', false);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, UsersRoles::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, RolesPermissions::class);
    }
}
