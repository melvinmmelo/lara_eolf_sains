<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class prices extends Model
{
    use HasFactory;
    protected $fillable = [
        'pricing_id',
        'p_code',
        'p_unit',
        'p_quant',
        'p_price'
        // Add other fillable attributes here if any
    ];

    // create a scope to get the price of a product and a price level
    public function scopeGetPrice($query, $p_code, $p_level)
    {
        return $query->where('p_code', $p_code)->where('p_level', $p_level)->first()->p_price;
    }

}
