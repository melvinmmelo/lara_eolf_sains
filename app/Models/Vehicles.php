<?php

namespace App\Models;

use App\Models\Concerns\AutoLogsChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model
{
    use HasFactory, AutoLogsChanges;

    protected $fillable = [
        'plateno',
        'brand',
        'description',
        'type',
        'size',
        'capacity',
        'remarks',
        'status',
        // Add other fillable attributes here if any
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at->format('m-d-Y h:i A');
    }
}
