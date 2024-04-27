<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhAddr extends Model
{
    protected $table = 'ph_addrs'; // Specify the table name if it's different from the model name convention

    protected $fillable = [
        'code', 'name', 'g_level'
    ];

    // Optionally, define any relationships with other models
    // For example, if you have relationships with cities, provinces, or regions, define them here

    // Example of a relationship with cities
    // public function cities()
    // {
    //     return $this->hasMany(City::class);
    // }
}
