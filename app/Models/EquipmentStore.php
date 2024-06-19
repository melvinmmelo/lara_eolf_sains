<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;

// class EquipmentStore extends Model
// {
//     protected $table = 'equipment_store';
//     use HasFactory;

//     public function equipment(): BelongsTo
//     {
//         return $this->belongsTo(Equipment::class);
//     }

//     public function store(): BelongsTo
//     {
//         return $this->belongsTo(StoreInfo::class);
//     }
//     public function customer()
// {
//     return $this->belongsTo(Customer::class, 'customer_id', 'id');
// }
// }


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentStore extends Model
{
    use HasFactory;

    protected $table = 'equipment_store';

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(StoreInfo::class, 'store_id');
    }

    public function storeinfo()
    {
        return $this->belongsTo(StoreInfo::class, 'store_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }

    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at->format('m-d-Y h:i A');
    }
}
