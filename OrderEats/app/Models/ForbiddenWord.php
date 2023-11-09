<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForbiddenWord extends Model
{
    protected $table = 'forbidden_words';

    protected $fillable = [
        'word'
    ];
}

