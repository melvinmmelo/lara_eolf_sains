<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'contact',
        'status',
        // Add other fillable attributes here if any
    ];

    // scope is active
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at->format('m-d-Y h:i A');
    }

    public function scopeBranch($query, $branchCode)
    {
        return $query->where('branch_code', $branchCode);
    }
}
