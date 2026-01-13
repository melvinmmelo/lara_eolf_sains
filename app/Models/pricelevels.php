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
        'pl_status',
        'pl_type'
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

    // Relationship to bad order prices
    public function badOrderPrices()
    {
        return $this->hasMany(BadOrderPrice::class, 'price_level_id', 'id');
    }

    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at->format('m-d-Y h:i A');
    }

    // get all price levels by branch code that is not bad pricing and is active
    public static function getPriceLevels($branchCode)
    {
        return pricelevels::branch($branchCode)->where('pl_type', '!=', 'BAD PRICING')->where('pl_status', 'Active')->get();
    }

}
