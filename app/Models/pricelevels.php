<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pricelevels extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_code',
        'pl_name',
        'pl_desc',
        'pl_status'
        // Add other fillable attributes here if any
    ];
}
