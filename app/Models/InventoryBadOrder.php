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
    ];

    protected $casts = [
        'products' => 'array',
        'date_created' => 'date',
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
}
