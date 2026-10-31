<?php
namespace App\Modules\Permission\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name','display_name','description','status'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }
}
