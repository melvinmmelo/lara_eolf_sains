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
        'modified_by'
    ];
}
