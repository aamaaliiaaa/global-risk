<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryIndicator extends Model
{
    protected $table = 'country_indicators';

    protected $fillable = [
        'country_id',
        'year',
        'gdp',
        'inflation',
        'population',
        'exports',
        'imports'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
