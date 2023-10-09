<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'user_id', 'shipper_id', 'menu_id', 'quantity', 'total_price', 'order_status', 
    ];
}
