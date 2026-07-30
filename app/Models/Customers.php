<?php

namespace App\Models;

use App\Models\Concerns\AutoLogsChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory, AutoLogsChanges;

    protected $guarded = [];

    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->middlename} {$this->lastname}";
    }

    public function stores()
    {
        return $this->hasMany(StoreInfo::class, 'customer_id');
    }

    public function equipmentStores()
    {
        return $this->hasMany(EquipmentStore::class, 'customer_id', 'id');
    }

    public function storeinfo()
        {
            return $this->hasOne(StoreInfo::class, 'customer_id');
        }

    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        // Nullable in the schema, and this accessor is in $appends — so an
        // unguarded ->format() fatals on any toJson()/toArray() of a row with
        // no created_at (e.g. a record restored from the activity log).
        return $this->created_at?->format('m-d-Y h:i A');
    }


    public function inbounds()
    {
        return $this->hasMany(Inbound::class, 'customer_id');
    }

    public function scopeBranch($query, $branchCode)
    {
        return $query->where('branch_code', $branchCode);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function badOrders()
    {
        return $this->hasMany(BadOrder::class, 'customer_id');
    }

    // Sales-side bad orders (the canonical system — see CLAUDE.md).
    public function newBadOrders()
    {
        return $this->hasMany(NewBadOrder::class, 'customer_id');
    }

    // The customer's explicitly-assigned price level (nullable).
    public function priceLevel()
    {
        return $this->belongsTo(pricelevels::class, 'pricelevel_id');
    }

    // Effective price level id for ordering: the customer's own assignment, or
    // the branch's designated default CUSTOMER level when none is set. May be
    // null if the customer has none and the branch has no default flagged.
    public function resolvedPriceLevelId()
    {
        return $this->pricelevel_id ?? pricelevels::defaultIdForBranch($this->branch_code);
    }
}
