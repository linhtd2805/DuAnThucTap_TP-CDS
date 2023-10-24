<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'user_id', 'shipper_id', 'menu_id', 'quantity', 'total_price', 'order_status', 
    ];

    public function users() 
    { 
        return $this->belongsTo(User::class, 'user_id');
    }

    public function menus() 
    { 
        return $this->belongsTo(Menus::class, 'menu_id'); 
    }
}
