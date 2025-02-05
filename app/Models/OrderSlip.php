<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSlip extends Model
{
    use HasFactory;

    protected $appends = ['f_created_at', 'r_created_at'];

    
    protected $fillable = [
        'branch_code',
        'code',
        'delivery_person',
        'driver_name',
        'total_amount',
        'checked_by',
        'generated_by',
        'remarks'
    ];

    public function getRCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('m/d/Y') : null;
    }

    public function getfCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d h:s A') : null;
    }

    public function scopeBranch($query, $branch_code)
    {
        return $query->where('branch_code', $branch_code);
    }

}
