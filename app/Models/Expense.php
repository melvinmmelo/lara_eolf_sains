<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'category',
        'amount',
        'date',
        'description',
        'receipt_path',
        'branch_code',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Scope a query to filter by branch code.
     */
    public function scopeBranch($query, $branch_code)
    {
        return $query->where('branch_code', $branch_code);
    }
}
