<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MaterialsInventory extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'branch_code',
        'name',
        'unit',
        'quantity',
        'amount',
        'location',
        'remarks',
        'modified_by',
        'requested_by',
        'issued_by',
        'withdrawal_date',
        'withdrawal_code',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'unit',
                'quantity',
                'amount',
                'location',
                'remarks',
                'modified_by',
                'withdrawal_date',
                'withdrawal_code',
                'withdrawal_id',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Material {$eventName}");
    }

    public function scopeBranch($query, $branch_code)
    {
        return $query->where('branch_code', $branch_code);
    }

    public function scopeActiveItems($query)
    {
        return $query->whereNull('withdrawal_id');
    }
}
