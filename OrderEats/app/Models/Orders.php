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
    public function shipper() 
    { 
        return $this->belongsTo(User::class, 'shipper_id'); 
    }

    public function getAllPaginated() {
        return $this->paginate(3); // Phân trang với 10 phần tử trên mỗi trang
    }
}
