<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Permission extends Model
{
    protected $fillable = [
        'name', 'slug'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class,'roles_permissions');
    }

    /**
     * Связь модели Permission с моделью User, позволяет получить
     * всех пользователей с этим правом
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'users_permissions');
    }
}
