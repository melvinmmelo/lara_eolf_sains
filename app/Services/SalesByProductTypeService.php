<?php

namespace App\Services;

use App\Models\ProductType;

/**
 * Aggregates inbound (sales) order lines by product type.
 *
 * Amounts are ORDER-LINE revenue only (quantity x line price). The order-level
 * service fee (is_with_sf), discounts and bad-order deductions are deliberately
 * excluded, because they cannot be attributed to a single product type — so the
 * totals here will NOT equal the sum of Inbound::getGrandTotalAttribute().
 * See docs/specs/001-sales-by-product-type.md.
 */
class SalesByProductTypeService
{
    /**
     * @param  iterable<\App\Models\Inbound>  $inbounds
     * @return array{rows: array<int, array<string, mixed>>, totals: array{quantity: float, amount: float}}
     */
    public static function summarize(iterable $inbounds): array
    {
        // One query for the whole report — never per line (the N+1 in
        // InboundService::getTotalOfAllInboundProducts).
        $types = ProductType::query()->get()->keyBy('code');

        $accumulated = [];

        foreach ($inbounds as $inbound) {
            foreach (self::lines($inbound->products) as $line) {
                $ptypeCode = $line['ptype_code'] ?? null;

                if ($ptypeCode === null || $ptypeCode === '') {
                    continue;
                }

                $quantity = (float) ($line['quantity'] ?? 0);
                $amount = $quantity * (float) ($line['price'] ?? 0);

                if (! isset($accumulated[$ptypeCode])) {
                    $accumulated[$ptypeCode] = ['quantity' => 0.0, 'amount' => 0.0];
                }

                $accumulated[$ptypeCode]['quantity'] += $quantity;
                $accumulated[$ptypeCode]['amount'] += $amount;
            }
        }

        $rows = [];

        foreach ($accumulated as $ptypeCode => $sums) {
            $type = $types->get($ptypeCode);

            $rows[] = [
                'ptype_code' => $ptypeCode,
                'name' => $type->name ?? $ptypeCode,
                'quantity' => $sums['quantity'],
                'amount' => $sums['amount'],
                // Unknown types sort last rather than being dropped.
                'sequence_no' => $type->sequence_no ?? PHP_INT_MAX,
            ];
        }

        usort($rows, fn ($a, $b) => [$a['sequence_no'], $a['name']] <=> [$b['sequence_no'], $b['name']]);

        return [
            'rows' => $rows,
            'totals' => [
                'quantity' => array_sum(array_column($rows, 'quantity')),
                'amount' => array_sum(array_column($rows, 'amount')),
            ],
        ];
    }

    /**
     * Decode an inbounds.products blob into its line items.
     *
     * `products` is usually a JSON array, but ~285 production rows store it as a
     * JSON OBJECT ("{"0":{...}}"). json_decode(..., true) normalises both to a
     * PHP array, so both shapes are counted — do not "optimise" this into
     * JSON_TABLE('$[*]'), which silently skips the object-encoded rows.
     *
     * @return array<int|string, array<string, mixed>>
     */
    private static function lines(mixed $products): array
    {
        if (is_array($products)) {
            $decoded = $products;
        } elseif (is_string($products) && $products !== '') {
            $decoded = json_decode($products, true);
        } else {
            return [];
        }

        if (! is_array($decoded)) {
            return [];
        }

        // Drop malformed entries so one bad row can't fatal the whole report.
        return array_filter($decoded, 'is_array');
    }
}
