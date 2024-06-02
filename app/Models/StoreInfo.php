<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreInfo extends Model
{
    use HasFactory;

    protected $table = 'storeinfo';

    protected $fillable = [
        'customer_id',
        'storename',
        'contactno',
        'region',
        'province',
        'city',
        'brgy',
        'subdivision',
        'longitude',
        'latitude',
        'listype',
        'length_stay',
        'remarks',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customers::class);
    }

    public function equipmentStores(): HasMany
    {
        return $this->hasMany(EquipmentStore::class, 'store_id');
    }
}
