<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drivers extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'contact',
        'status',
        'default_price_level',
        'designation',
    ];

    // scope is active
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    // scope for designation is salesman
    public function scopePerDesignation($query, $designation)
    {
        return $query->where('designation', $designation);
    }

    public function priceLevel()
    {
        return $this->belongsTo(pricelevels::class, 'default_price_level');
    }

    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at->format('m-d-Y h:i A');
    }
}
