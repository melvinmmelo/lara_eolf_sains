<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;


    protected $fillable = [
        'ownership',
        'type',
        'brand',
        'price',
        'serial_no',
        'code',
        'distributor',
        'date_delivered',
        'date_purchased',
    ];

    public function equipmentStore()
    {
        return $this->hasOne(EquipmentStore::class);
    }

    // create a scope for available equipment
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    // CREATE A SCOPE FOR NOT AVAILABLE EQUIPMENT
    public function scopeNotAvailable($query)
    {
        return $query->where('status', 'added');
    }

    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at->format('m-d-Y h:i A');
    }
}
