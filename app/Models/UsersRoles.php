<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UsersRoles extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roles()
    {
        return $this->belongsTo(Role::class);
    }
}
