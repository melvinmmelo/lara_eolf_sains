<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPurchaseReceipt extends Model
{
    use HasFactory;

    protected $fillable = ['branch', 'dr_no', 'issue_date', 'status', 'products', 'user_id'];
}
