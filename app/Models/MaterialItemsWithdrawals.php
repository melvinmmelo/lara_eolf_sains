<?php

namespace App\Models;

use App\Models\Concerns\AutoLogsChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialItemsWithdrawals extends Model
{
    use HasFactory, AutoLogsChanges;

    protected $fillable = [
        'code',
        'requested_by',
        'issued_by',
        'withdrawal_date',
    ];

    protected $casts = [
        'withdrawal_date' => 'date',
    ];

    public function materials()
    {
        return $this->hasMany(MaterialsInventory::class, 'withdrawal_id');
    }
}
