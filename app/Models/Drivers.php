<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drivers extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'contact',
        'status',
        // Add other fillable attributes here if any
    ];

    // scope is active
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function priceLevel()
    {
        return $this->belongsTo(pricelevels::class, 'default_price_level');
    }
}
