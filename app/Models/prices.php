<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class prices extends Model
{
    use HasFactory;
    protected $fillable = [
        'p_level',
        'p_code',
        'p_unit',
        'p_quant',
        'p_price'
        // Add other fillable attributes here if any
    ];
}
