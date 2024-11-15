<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_code',
        'code',
        'inbound_id',
        'address',
        'customer_name',
        'date',
        'status',
        'generated_by',
    ];

    protected $appends = ['f_created_at'];

    protected $casts = [
        'date' => 'datetime'
    ];

    public static function countPerYear($year)
    {
        return self::whereYear('created_at', $year)->count();
    }
    public function inbound()
    {
        return $this->belongsTo(Inbound::class);
    }

    public function getfCreatedAtAttribute()
    {
        return $this->date ? $this->date->format('Y-m-d') : null;
    }

    public static function generateCode($branchCode)
    {

        if ($branchCode == 'EFTO-CAG') {
            $prefix = 'C';
        } else {
            $prefix = 'T';
        }

        return "DR-" . $prefix . str_pad(self::count() + 1, 4, "0", STR_PAD_LEFT);
    }
}
