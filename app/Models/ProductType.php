<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

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
