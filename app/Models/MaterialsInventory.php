<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialsInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_code',
        'name',
        'unit',
        'quantity',
        'amount',
        'location',
        'remarks',
        'modified_by',
        'requested_by',
        'issued_by',
        'withdrawal_date',
        'withdrawal_code',
    ];

    public function scopeBranch($query, $branch_code)
    {
        return $query->where('branch_code', $branch_code);
    }

    public function scopeActiveItems($query)
    {
        return $query->whereNull('withdrawal_id');
    }
}
