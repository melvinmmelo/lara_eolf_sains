<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pricelevels extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_code',
        'pl_name',
        'pl_desc',
        'pl_status'
        // Add other fillable attributes here if any
    ];

    public function scopeBranch($query, $branch_code)
    {
        return $query->where('branch_code', $branch_code);
    }

    // CREATE A RELATIONSHIP BETWEEN PRICELEVELS AND PRICES
    public function prices()
    {
        return $this->hasMany(prices::class);
    }
}
