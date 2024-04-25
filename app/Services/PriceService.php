<?php

namespace App\Services;

use App\Models\prices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceService extends Model
{
    use HasFactory;


    public static function getPrice($productCode)
    {
        $price = prices::where('p_code', $productCode)->orderBy('created_at', 'desc')->first();
        return $price;
    }

}
