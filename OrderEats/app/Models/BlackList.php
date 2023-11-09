<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlackList extends Model
{
    protected $fillable = ['word']; 
    protected $table = 'black_list';

    // Định nghĩa một phương thức tìm từ nhạy cảm trong danh sách
    public function scopeIsSensitive($query, $word)
    {
        return $query->where('word', 'like', "%$word%");
    }
}
