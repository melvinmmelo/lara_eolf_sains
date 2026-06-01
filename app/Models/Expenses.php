<?php

namespace App\Models;

use App\Models\Concerns\AutoLogsChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    use HasFactory, AutoLogsChanges;

    protected $fillable = [
        'branch_code',
        'expense_date',
        'category',
        'particulars',
        'payee',
        'amount',
        'payment_method',
        'reference_no',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Common expense categories for this operation
    public const CATEGORIES = [
        'Fuel',
        'Salaries & Wages',
        'Repairs & Maintenance',
        'Utilities',
        'Rent',
        'Supplies',
        'Transportation',
        'Communication',
        'Permits & Licenses',
        'Miscellaneous',
    ];

    public const PAYMENT_METHODS = [
        'Cash',
        'Bank Transfer',
        'Check',
        'GCash',
    ];

    public function scopeBranch($query, $branch_code)
    {
        return $query->where('branch_code', $branch_code);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
