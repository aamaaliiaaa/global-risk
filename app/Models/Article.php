<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'title',
        'content',
        'author',
        'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime'
    ];
}
