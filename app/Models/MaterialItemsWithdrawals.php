<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialItemsWithdrawals extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'requested_by',
        'issued_by',
    ];
}
