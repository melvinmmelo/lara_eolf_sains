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

    protected $appends = ['f_created_at', 'code'];

    protected $casts = [
        'date' => 'datetime'
    ];

    public static function countPerYear($year)
    {
        return self::whereYear('created_at', $year)->count();
    }

    public function getCodeAttribute()
    {
        return $this->created_at->format('y') . "-" . str_pad($this->id, 5, "0", STR_PAD_LEFT);
    }

    public function inbound()
    {
        return $this->belongsTo(Inbound::class);
    }

    public function getfCreatedAtAttribute()
    {
        return $this->date ? $this->date->format('Y-m-d') : null;
    }
}
