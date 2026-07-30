<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PullOutForm;

class EquipmentStore extends Model
{
    use HasFactory;

    protected $table = 'equipment_store';

    protected $fillable = [
        'top_freezer_remarks',
        'notes_free_small_cup',
        'checker_name',
        'loader_name',
        'remarks_gatepass',
        'has_ice_scraper',
        'has_lock_and_key',
        'has_signage_bracket',
        'has_tarpaulin_logo',
        'has_tarpaulin_pricelist',
    ];

    protected $casts = [
        'has_ice_scraper' => 'boolean',
        'has_lock_and_key' => 'boolean',
        'has_signage_bracket' => 'boolean',
        'has_tarpaulin_logo' => 'boolean',
        'has_tarpaulin_pricelist' => 'boolean'
    ];

    protected $with = ['customer', 'equipment'];

    protected $appends = ['date_created'];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(StoreInfo::class, 'store_id');
    }

    public function storeinfo()
    {
        return $this->belongsTo(StoreInfo::class, 'store_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }

    public function getDateCreatedAttribute()
    {
        return $this->created_at?->format('m-d-Y h:i A');
    }

    // connect to equipment table and get the equipment with specified branch code
    public function scopeEquipmentByBranch($branch_code)
    {
        return $this->belongsTo(Equipment::class)->where('branch_code', $branch_code);
    }

}
