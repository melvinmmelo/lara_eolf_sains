<?php

namespace App\Models;

use App\Models\Concerns\AutoLogsChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory, AutoLogsChanges;

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    // scope where code is
    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    // Relationship to bad order prices
    public function badOrderPrices()
    {
        return $this->hasMany(BadOrderPrice::class, 'ptype_code', 'code');
    }

    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at?->format('m-d-Y h:i A');
    }

}
