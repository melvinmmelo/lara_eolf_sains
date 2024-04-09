<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model
{
    use HasFactory;

    protected $fillable = [
        'plateno',
        'brand',
        'description',
        'type',
        'size',
        'capacity',
        'remarks',
        'status',
        // Add other fillable attributes here if any
    ];
}
