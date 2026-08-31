<?php

namespace App\Services;

/**
 * Aggregates inbound (sales) orders per freezer (DEGIC code) per month.
 *
 * A "freezer" row is identified by the denormalized inbounds.degic_no plus the
 * customer_id it was sold under — the same customer can hold several freezers,
 * and orders with no DEGIC code still belong to a customer. Amounts are
 * ORDER-LINE revenue only (quantity x line price); the order-level service fee
 * (is_with_sf), discounts and bad-order deductions are excluded, matching
 * SalesByProductTypeService — so totals will NOT equal the Sales report's
 * grand total. See docs/specs/003-sales-by-freezer.md.
 */
class SalesByFreezerService
{
    /**
     * @param  iterable<\App\Models\Inbound>  $inbounds
     * @return array{
     *     rows: array<int, array{degic_no: string, customer_name: string, store_name: string, months: array<int, float>, total: float}>,
     *     totals: array{months: array<int, float>, grand: float}
     * }
     */
    public static function summarize(iterable $inbounds): array
    {
        $accumulated = [];

        foreach ($inbounds as $inbound) {
            if (! $inbound->order_date) {
                continue;
            }

            $amount = self::lineRevenue($inbound->products);
            $month = (int) $inbound->order_date->month;

            $degic = trim((string) $inbound->degic_no);
            $key = ($degic !== '' ? $degic : '~none').'|'.$inbound->customer_id;

            if (! isset($accumulated[$key])) {
                $accumulated[$key] = [
                    'degic_no' => $degic !== '' ? $degic : 'N/A',
                    'customer_name' => (string) $inbound->customer_name,
                    'store_name' => (string) $inbound->store_name,
                    'months' => array_fill(1, 12, 0.0),
                    'total' => 0.0,
                ];
            }

            $accumulated[$key]['months'][$month] += $amount;
            $accumulated[$key]['total'] += $amount;
        }

        $rows = array_values($accumulated);

        usort($rows, fn ($a, $b) => [
            mb_strtolower($a['customer_name']), $a['degic_no'],
        ] <=> [
            mb_strtolower($b['customer_name']), $b['degic_no'],
        ]);

        $monthTotals = array_fill(1, 12, 0.0);
        foreach ($rows as $row) {
            foreach ($row['months'] as $month => $amount) {
                $monthTotals[$month] += $amount;
            }
        }

        return [
            'rows' => $rows,
            'totals' => [
                'months' => $monthTotals,
                'grand' => array_sum($monthTotals),
            ],
        ];
    }

    /**
     * Sum quantity x price over an inbounds.products blob. Same decode rules as
     * SalesByProductTypeService::lines() — the blob is usually a JSON array but
     * ~285 production rows are object-encoded ({"0":{...}}), and both must count.
     */
    private static function lineRevenue(mixed $products): float
    {
        if (is_array($products)) {
            $decoded = $products;
        } elseif (is_string($products) && $products !== '') {
            $decoded = json_decode($products, true);
        } else {
            return 0.0;
        }

        if (! is_array($decoded)) {
            return 0.0;
        }

        $total = 0.0;

        foreach ($decoded as $line) {
            if (! is_array($line)) {
                continue;
            }

            $total += (float) ($line['quantity'] ?? 0) * (float) ($line['price'] ?? 0);
        }

        return $total;
    }
}
