<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class prices extends Model
{
    use HasFactory;
    protected $fillable = [
        'pricelevel_id',
        'p_code',
        'p_unit',
        'p_quant',
        'p_price'
        // Add other fillable attributes here if any
    ];

    // create a relationship that this is belong to a pricing level
    public function pricelevel()
    {
        return $this->belongsTo(pricelevels::class);
    }

    // create a that gets the price of a product that based on the pricing level and product code
    public static function getPrice($productCode, $branchCode)
    {
        $price = prices::where('p_code', $productCode)->whereHas('pricelevel', function($query) use ($branchCode){
            $query->where('branch_code', $branchCode);
        })->first()->p_price;

        return $price;
    }

}
