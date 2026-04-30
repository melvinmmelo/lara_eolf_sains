<?php

namespace App\Models;

use App\Models\Concerns\AutoLogsChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory, AutoLogsChanges;

    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // protected $with = ['productType', 'productVariant'];

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_code', 'code');
    }

    public function productVariant(): BelongsTo
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_active', false);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->code = $model->product_type_code . '_' . $model->product_variant_code;
        });
    }

    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at->format('m-d-Y h:i A');
    }
}
