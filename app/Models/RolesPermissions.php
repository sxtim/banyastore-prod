<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RolesPermissions extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'permission_id'
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
