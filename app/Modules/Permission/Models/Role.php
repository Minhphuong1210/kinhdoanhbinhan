<?php
namespace App\Modules\Permission\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name','display_name','description'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'role_admin', 'role_id', 'admin_id');
    }
}
