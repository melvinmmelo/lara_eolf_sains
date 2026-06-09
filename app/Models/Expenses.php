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
        'payee_address',
        'taxpayer_type',
        'tin',
        'amount',
        'payment_method',
        'reference_no',
        'petty_cash_no',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Expense categories (chart-of-accounts order, not alphabetical).
    public const CATEGORIES = [
        'Direct Materials (Dan Eric)',
        'On Call',
        'Other Direct Cost (Packaging)',
        'Internet',
        'Light and Water Bill',
        'Meal Allowance-Admin',
        'Donation',
        'Fuel (Delivery, Motor)',
        'Insurance Expenses (Axa)',
        'Meal Allowance-Delivery',
        'Miscellaneous Expense',
        'Services Charges (Bank, Gcash, Payment)',
        'Other Expenses (Cleaning Materials)',
        'Allowance Sir JDC',
        'Lodging (Delivery)',
        'Professional Fee',
        'Promotion and Advertising (Freezer)',
        'Property and Equipment',
        'Rentals (Warehouse, Office, Staff house)',
        'Repairs and Maintenance (PMS)',
        'Toll (Delivery)',
        'Salaries',
        'Seminars and Trainings',
        'SSS',
        'Philhealth',
        'Pag ibig Contribution',
        'SSS Loan',
        'Office Supplies',
        'Incentive (Freezer, Advertisement)',
        'Employee Benefits (Rice, Drinking Water)',
        'Taxes and Licenses (Government permits)',
        'Parking',
        'Transportation (Truck Ban)',
        'Transportation',
        'Cash Advance (Employee)',
        'Load',
    ];

    public const PAYMENT_METHODS = [
        'Cash',
        'Bank Transfer',
        'Check',
        'GCash',
    ];

    // Taxpayer classification of the payee, for BIR/VAT bookkeeping.
    public const TAXPAYER_TYPES = [
        'VAT',
        'Non-VAT',
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
