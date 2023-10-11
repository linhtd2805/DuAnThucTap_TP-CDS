<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    // public $timestamps = false; 
    protected $fillable = [ 'order_id', 'rating', 'comment', 'date', ]; 
    public function orders() 
    { 
        return $this->belongsTo(Orders::class, 'order_id'); 
    }

    public function user() 
    { 
        return $this->belongsTo(User::class, 'user_id'); 
    }
}
