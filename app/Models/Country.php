<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    Protected $fillable = [
        'flag',
        'name',
        'risk',
        'wheather',
        'currency',
        'latitude',
        'longitude',
    ];
}
