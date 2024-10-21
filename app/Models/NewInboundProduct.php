<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewInboundProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'inbound_id',
        'branch_code',
        'order',
        'ptype_code',
        'code',
        'description',
        'old_quantity',
        'quantity',
        'price',
        'unit',
        'user_id'
    ];

    public function inbound()
    {
        return $this->belongsTo(Inbound::class);
    }
}
