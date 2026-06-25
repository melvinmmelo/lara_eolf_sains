<?php

namespace App\Services;

use App\Models\Inbound;
use App\Models\InboundLine;
use Illuminate\Support\Facades\DB;

/**
 * Builds the inbound_lines projection for a single inbound from its products
 * JSON. Source of truth is always Inbound::$products; this only ever writes to
 * inbound_lines, so it can be re-run any time to rebuild from the blob.
 *
 * Used by the inbound:project-lines command (bulk backfill / rebuild) and by
 * the Inbound `saved` observer (incremental sync). Idempotent per inbound:
 * existing lines are replaced inside a transaction.
 */
class InboundLineProjector
{
    /**
     * Rebuild inbound_lines for one inbound. Returns the number of lines written.
     */
    public function project(Inbound $inbound): int
    {
        $items = json_decode((string) $inbound->products, true);

        return DB::transaction(function () use ($inbound, $items) {
            InboundLine::where('inbound_id', $inbound->id)->delete();

            if (! is_array($items) || $items === []) {
                return 0;
            }

            $now = now();
            $rows = [];
            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $qty = (int) round((float) ($item['quantity'] ?? 0));
                $price = round((float) ($item['price'] ?? 0), 2);
                $rows[] = [
                    'inbound_id' => $inbound->id,                 // parent's real id (JSON inbound_id is unreliable)
                    'branch_code' => $inbound->branch_code,
                    'order_date' => $inbound->order_date,
                    'product_code' => $item['code'] ?? null,
                    'ptype_code' => $item['ptype_code'] ?? null,
                    'description' => isset($item['description']) ? mb_substr((string) $item['description'], 0, 255) : null,
                    'unit' => isset($item['unit']) ? mb_substr((string) $item['unit'], 0, 50) : null,
                    'quantity' => $qty,
                    'price' => $price,
                    'line_total' => round($qty * $price, 2),
                    'line_order' => isset($item['order']) && is_numeric($item['order']) ? (int) $item['order'] : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                InboundLine::insert($chunk);
            }

            return count($rows);
        });
    }

    /**
     * The line-item total computed straight from the JSON blob, for drift checks.
     */
    public function jsonLineTotal(Inbound $inbound): float
    {
        $items = json_decode((string) $inbound->products, true);
        if (! is_array($items)) {
            return 0.0;
        }
        $sum = 0.0;
        foreach ($items as $item) {
            if (is_array($item)) {
                $sum += (int) round((float) ($item['quantity'] ?? 0)) * round((float) ($item['price'] ?? 0), 2);
            }
        }

        return round($sum, 2);
    }
}
