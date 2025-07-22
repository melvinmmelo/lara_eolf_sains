<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class InventoryBadOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_code',
        'reference_name',
        'products',
        'user_id',
        'status',
        'remarks',
        'date_created',
        'is_rolled_back',
        'rolled_back_at',
        'rolled_back_by',
        'rollback_reason',
    ];

    protected $casts = [
        'products' => 'array',
        'date_created' => 'date',
        'rolled_back_at' => 'datetime',
        'is_rolled_back' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateReferenceName()
    {
        $latest = static::count();
        $prefix = 'INVBO-';
        $created_at = date('Ymd');
        $nextNumber = $latest ? (int) $latest + 1 : 1;
        return $prefix . $created_at . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);      
    }

    public function rolledBackBy()
    {
        return $this->belongsTo(User::class, 'rolled_back_by');
    }

    public function canRollback()
    {
        return !$this->is_rolled_back && $this->status === 'saved';
    }

    public function rollback($reason = null, $userId = null)
    {
        if (!$this->canRollback()) {
            throw new \Exception('This bad order cannot be rolled back.');
        }

        $this->update([
            'is_rolled_back' => true,
            'rolled_back_at' => now(),
            'rolled_back_by' => $userId ?? auth()->id(),
            'rollback_reason' => $reason,
            'status' => 'rolled_back',
        ]);

        return $this;
    }
}
