<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
    protected $fillable = ['item_name','description','price'];

    protected $table = 'menus';

    public function getAllPaginated() {
        return $this->paginate(2); // Phân trang với 10 phần tử trên mỗi trang
    }

}
