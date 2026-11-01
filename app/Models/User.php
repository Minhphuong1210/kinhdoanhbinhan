<?php

namespace App\Models;

use App\Modules\Permission\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','tel','address','active','is_admin',
        'parent_id','vi_tien','pin','invite_by','image',
        'province_id','district_id','ward_id',
        'ngan_hang','stk','vi_tien_status','level','ngan_hang_id','chu_tk',
        'ngay_tham_gia','ngay_mua_don_cuoi','so_thanh_vien_gt','fcm','chi_nhanh_ngan_hang','cccd','vi_tien_mua_hang','bank_area_id'
    ];

    protected $hidden = ['password','remember_token','pin'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin == 1;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_admin', 'admin_id', 'role_id');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles->contains('name', $roleName);
    }

    public function hasPermission(string $permName): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }
        return $this->roles->flatMap->permissions->contains('name', $permName);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }


    public function inviteBy()
    {
        return $this->belongsTo(self::class, 'invite_by');
    }
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function dangKyXacNhanHtx()
    {
        return $this->hasOne(DangKyXacNhanHtx::class, 'admin_id');
    }
}
