<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewTempBadOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_bo_id',
        'ptype_code',
        'description',
        'quantity',
        'price',
    ];

    // accessor for price * quantity
    public function getAmountAttribute()
    {
        return $this->price * $this->quantity;
    }
}
