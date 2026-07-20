<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyCache extends Model
{
    protected $table = 'currency_cache';

    protected $fillable = [
        'currency_code',
        'rate',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];
}
