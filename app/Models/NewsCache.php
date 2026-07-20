<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCache extends Model
{
    protected $table = 'news_cache';

    protected $fillable = [
        'country_id',
        'title',
        'source',
        'description',
        'content',
        'url',
        'published_at',
        'sentiment',
        'positive_count',
        'negative_count',
        'expires_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
