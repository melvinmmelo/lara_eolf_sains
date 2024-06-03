<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // protected $with = ['productType', 'productVariant'];

    public function productType() : BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_code', 'code');
    }

    public function productVariant() : BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_code', 'code');
    }

    public function getProductNameAttribute()
    {
        return $this->productType->name . ' ' . $this->productVariant->name;
    }

    public function scopeProductCode($query, $pCode)
    {
        return $query->where('code', $pCode);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->code = $model->product_type_code . '_' . $model->product_variant_code;
        });
    }
}
