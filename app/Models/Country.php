<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'flag',
        'name',
        'risk',
        'risk_score',
        'weather',
        'currency',
        'latitude',
        'longitude',
    ];

    public function ports()
    {
        return $this->hasMany(Port::class);
    }
}
