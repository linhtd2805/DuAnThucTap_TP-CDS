<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reviews extends Model
{
    protected $fillable = [ 'order_id', 'rating', 'comment', 'date', ]; 
    public function orders() 
    { 
        return $this->belongsTo(Orders::class, 'order_id'); 
    }

    public function user() 
    { 
        return $this->belongsTo(User::class, 'user_id'); 
    }

    use SoftDeletes;
}
