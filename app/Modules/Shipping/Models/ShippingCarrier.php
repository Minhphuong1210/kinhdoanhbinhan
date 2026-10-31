<?php

namespace App\Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCarrier extends Model
{
    protected $fillable = [
        'name', 'alias', 'api_token', 'config', 'is_active', 'is_default'
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];
}
