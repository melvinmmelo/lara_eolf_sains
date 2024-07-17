<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_no',
        'degic_no',
        'customer_id',
        'customer_name',
        'date_assigned',
        'user_name_assigned',
        'date_pulled_out',
        'user_name_pulled_out',
        'pull_out_reason',
        'current_user_name',
    ];
}
