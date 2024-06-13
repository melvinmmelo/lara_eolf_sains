<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempBadOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'inbound_id','store_id', 'ptype_code', 'code', 'description', 
        'quantity', 'price', 'unit', 'amount'
    ];
}
