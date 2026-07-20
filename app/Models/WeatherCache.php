<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherCache extends Model
{
    protected $table = 'weather_cache';

    protected $fillable = [
        'weatherable_type',
        'weatherable_id',
        'temperature',
        'wind_speed',
        'weather_code',
        'condition',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function weatherable()
    {
        return $this->morphTo();
    }
}
