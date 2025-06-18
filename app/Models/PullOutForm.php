<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Customers as Customer;
use App\Models\Equipment;
use App\Models\EquipmentStore;

class PullOutForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pull_out_forms';

    protected $fillable = [
        'pof_no',
        'customer_name',
        'customer_id',
        'degic_no',
        'address',
        'sales_agent',
        'date',
        'pullout_model_serial_no',
        'pullout_degic_no',
        'pullout_pr_no',
        'pullout_cv_no',
        'pullout_rs_no',
        'refund_deposit',
        'replaced_model_serial_no',
        'replaced_degic_no',
        'replaced_lock_key',
        'replaced_signage',
        'replaced_equipment_json',
        'defective_compressor',
        'not_cooling',
        'stop_selling',
        'system_leak',
        'condemned',
        'return_to_supplier',
        'remarks',
        'prepared_by',
        'noted_by',
        'pullout_by',
        'customer_signature'
    ];

    protected $casts = [
        'date' => 'date',
        'defective_compressor' => 'boolean',
        'not_cooling' => 'boolean',
        'stop_selling' => 'boolean',
        'system_leak' => 'boolean',
        'condemned' => 'boolean',
        'return_to_supplier' => 'boolean',
        'refund_deposit' => 'decimal:2',
        'replaced_equipment_json' => 'array'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id');
    }

    public function replacementEquipment()
    {
        return $this->belongsTo(Equipment::class, 'replaced_degic_no', 'code');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pullOutForm) {
            if (empty($pullOutForm->pof_no)) {
                $pullOutForm->pof_no = static::generatePOFNumber();
            }
        });
    }

    protected static function generatePOFNumber()
    {
        $lastPOF = static::orderBy('id', 'desc')->first();
        $number = $lastPOF ? intval(substr($lastPOF->pof_no, 4)) + 1 : 1;
        return 'POF-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

}