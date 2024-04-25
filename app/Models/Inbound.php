<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inbound extends Model
{
    use HasFactory;

    protected $appends = ['f_created_at', 'f_updated_at'];

    public function driver() : BelongsTo {
        return $this->belongsTo(Drivers::class);
    }

    public function vehicle() : BelongsTo {
        return $this->belongsTo(Vehicles::class);
    }

    public function getfCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d h:s A') : null;
    }

    public function getfUpdatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d h:s A') : null;
    }
}
