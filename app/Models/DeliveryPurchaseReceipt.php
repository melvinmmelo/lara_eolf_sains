<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPurchaseReceipt extends Model
{
    use HasFactory;

    protected $fillable = ['branch_code', 'dr_no', 'issue_date', 'status', 'products', 'user_id'];

    //create a scope gets all the delivery purchase receipts from specific branch
    public function scopeBranch($query, $branch_code)
    {
        return $query->where('branch_code', $branch_code);
    }
}
