<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreInfo extends Model
{
    // Your model code here
    protected $table = 'storeinfo';
    
    protected $fillable = [
        'customer_id',
        // Add other fillable fields here
        'storename',
        'contactno',
        'region',
        'province',
        'city',
        'brgy',
        'subdivision',
        'longitude',
        'latitude',
        'listype',
        'length_stay',
        'remarks',
    ];
}
