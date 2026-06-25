<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One projected line of an inbound order (Phase 4). Derived from
 * inbounds.products JSON; rebuildable via `inbound:project-lines`. Not edited
 * directly — the JSON blob on Inbound is the source of truth.
 */
class InboundLine extends Model
{
    protected $fillable = [
        'inbound_id',
        'branch_code',
        'order_date',
        'product_code',
        'ptype_code',
        'description',
        'unit',
        'quantity',
        'price',
        'line_total',
        'line_order',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'line_order' => 'integer',
    ];

    public function inbound(): BelongsTo
    {
        return $this->belongsTo(Inbound::class, 'inbound_id');
    }

    public function scopeBranch($query, $branchCode)
    {
        return $query->where('branch_code', $branchCode);
    }
}
