<?php

namespace App\Models;

use App\Models\Concerns\AutoLogsChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory, AutoLogsChanges;

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    // scope is active
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    // scope where code is
    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }
    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at->format('m-d-Y h:i A');
    }

}
