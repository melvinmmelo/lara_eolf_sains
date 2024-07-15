<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Customers extends Model
// {
//     use HasFactory;

//     protected $guarded = [];

//     public function getFullNameAttribute()
//     {
//         return "{$this->firstname} {$this->lastname}";
//     }

//     public function stores()
//     {
//         return $this->hasMany(StoreInfo::class, 'customer_id');
//     }

//     // Accessor to concatenate the address components
//     public function getStoreAddressAttribute()
//     {
//         return "{$this->brgy}, {$this->subdivision}, {$this->city}";
//     }

//     public function equipmentStores()
//     {
//         return $this->hasMany(EquipmentStore::class, 'customer_id', 'id');
//     }
// }

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
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
        return $this->created_at->format('m-d-Y h:i A');
    }


    public function inbounds()
    {
        return $this->hasMany(Inbound::class, 'customer_id');
    }

    public function scopeBranchCode($query, $branchCode)
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
}
