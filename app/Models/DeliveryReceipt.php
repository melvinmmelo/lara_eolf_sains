<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'dr_no',
        'date',
        'generated_by',
    ];
}
