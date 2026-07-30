<?php

namespace App\Models;

use App\Models\Concerns\AutoLogsChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreInfo extends Model
{
    use AutoLogsChanges, HasFactory;

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

    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at?->format('m-d-Y h:i A');
    }
    
    public function badOrders()
    {
        return $this->hasMany(BadOrder::class, 'store_id');
    }

    public function inbounds(): HasMany
    {
        return $this->hasMany(Inbound::class, 'store_id');
    }

    /**
     * Does this store own records that would be orphaned by deleting it?
     *
     * There are no foreign keys on this schema. Three stores have already been
     * hard-deleted out from under 52 paid orders (2026-05-11, 2026-06-17,
     * 2026-07-15 — the same three requests that destroyed customers 246, 453
     * and 684). Those orders only still read correctly because inbounds
     * denormalizes store_name. Nothing may delete a store without asking this.
     */
    public function hasTransactionHistory(): bool
    {
        return $this->inbounds()->exists()
            || $this->equipmentStores()->exists()
            || $this->badOrders()->exists();
    }
}
