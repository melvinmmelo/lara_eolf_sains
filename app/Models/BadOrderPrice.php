<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadOrderPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'ptype_code',
        'ptype_name',
        'price_level_id',
        'price'
    ];

    protected $appends = ['date_created'];

    /**
     * Get the product type that owns this bad order price.
     */
    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'ptype_code', 'code');
    }

    /**
     * Get the price level that owns this bad order price.
     */
    public function priceLevel()
    {
        return $this->belongsTo(pricelevels::class, 'price_level_id', 'id');
    }

    /**
     * Get formatted creation date.
     */
    public function getDateCreatedAttribute()
    {
        return $this->created_at?->format('m-d-Y h:i A');
    }

    /**
     * Get bad order price by product type code and price level ID.
     */
    public static function getPrice($ptypeCode, $priceLevelId)
    {
        $badOrderPrice = self::where('ptype_code', $ptypeCode)
            ->where('price_level_id', $priceLevelId)
            ->first();

        return $badOrderPrice ? $badOrderPrice->price : null;
    }

    /**
     * Get all bad order prices for a specific price level.
     */
    public static function getPricesByPriceLevel($priceLevelId)
    {
        return self::where('price_level_id', $priceLevelId)->get();
    }

    /**
     * Get all bad order prices for a specific product type.
     */
    public static function getPricesByProductType($ptypeCode)
    {
        return self::where('ptype_code', $ptypeCode)->get();
    }
}
