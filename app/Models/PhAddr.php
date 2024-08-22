<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhAddr extends Model
{
    protected $table = 'ph_addrs'; // Specify the table name if it's different from the model name convention

    protected $fillable = [
        'code', 'name', 'g_level'
    ];
}
