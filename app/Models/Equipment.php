<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_code',
        'ownership',
        'type',
        'brand',
        'price',
        'serial_no',
        'model',
        'code',
        'distributor',
        'date_delivered',
        'date_purchased',
        'status',
        'assignment_history',
    ];

    public function equipmentStore()
    {
        return $this->hasOne(EquipmentStore::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeNotAvailable($query)
    {
        return $query->where('status', 'added');
    }

    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at->format('m-d-Y h:i A');
    }

    public function scopeBranch($query, $branch_code)
    {
        return $query->where('branch_code', $branch_code);
    }
}
