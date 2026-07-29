<?php

namespace App\Services;

use App\Models\BadOrder;
use App\Models\Inbound;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ! ALL STATIC FUNCTIONS
class InboundService extends Model
{
    use HasFactory;

    public static function getTotalOfInboundProducts($inboundId)
    {
        $inbound = Inbound::find($inboundId);
        $products = json_decode($inbound->products, true);
        $total = 0;

        if ($products) {
            foreach ($products as $product) {
                $total += $product['quantity'] * $product['price'];
            }
        }

        return $total;
    }

    public static function getTotalOfBadOrderOfInboundId($inboundId)
    {

        $inbound = BadOrder::ofInboundId($inboundId)->get();

        if ($inbound) {

            $total = 0;
            foreach ($inbound as $badOrder) {
                $total += $badOrder->amount;
            }

            return $total;
        }

        return 0;
    }

    // per date
    // default is today
    public static function getTotalOfAllInboundProducts($branchCode, $fromDate = null, $toDate = null)
    {

        if ($fromDate && $toDate) {
            $fromDate = date('Y-m-d', strtotime($fromDate));
            $toDate = date('Y-m-d', strtotime($toDate));

            $inbounds = Inbound::where('branch_code', $branchCode)
                ->whereBetween('order_date', [$fromDate, $toDate])->whereNotIn('status', ['Cancelled', 'Wrong entry', 'Deleted'])
                ->get();

        } else {

            $dateToExtract = date('Y-m-d');

            $inbounds = Inbound::where('branch_code', $branchCode)
                ->whereDate('order_date', $dateToExtract)->whereNotIn('status', ['Cancelled', 'Wrong entry', 'Deleted'])
                ->get();

        }

        return self::summarizeProductsByCode($inbounds);
    }

    public static function getTotalOfAllInboundProductsv2($branchCode) // ! PANG OVERALL
    {

        $inbounds = Inbound::where('branch_code', $branchCode)->whereNotIn('status', ['Cancelled', 'Wrong entry', 'Deleted'])->get();

        // $inbounds = Inbound::where('branch_code', $branchCode)->whereBetween('order_date', ['2024-08-19', '2024-08-21'])->get(); // ! uncomment if you want to filter by range of dates
        return self::summarizeProductsByCode($inbounds);
    }

    /**
     * Total quantity per product CODE, ordered by the product type's sequence_no.
     *
     * Shared by both getTotalOfAllInboundProducts* methods, which previously
     * carried identical copies of this block.
     *
     * The product-type lookup is fetched ONCE up front. It used to run
     * `ProductType::code($code)->first()` inside the per-line map — 9,040
     * queries for a single month of one branch. It is now 1.
     *
     * Also hardened: a line naming a product type that no longer exists used to
     * fatal on `$productType->sequence_no` (null dereference); such lines now
     * sort last instead. Malformed lines are skipped rather than fataling.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\Inbound>  $inbounds
     * @return array<int, array{code: mixed, quantity: mixed, sequence_no: int}>
     */
    private static function summarizeProductsByCode($inbounds): array
    {
        $sequenceByType = ProductType::pluck('sequence_no', 'code');

        return $inbounds->flatMap(function ($inbound) use ($sequenceByType) {
            // json_decode(..., true) normalises both the array and the
            // object-encoded ("{"0":{...}}") shapes found in production.
            return collect(json_decode($inbound->products, true) ?: [])
                ->filter(fn ($product) => is_array($product) && isset($product['code']))
                ->map(fn ($product) => [
                    'code' => $product['code'],
                    'quantity' => $product['quantity'] ?? 0,
                    'sequence_no' => $sequenceByType[$product['ptype_code'] ?? ''] ?? PHP_INT_MAX,
                ]);
        })
            ->groupBy('code')
            ->map(function ($group) {
                return [
                    'code' => $group->first()['code'],
                    'quantity' => $group->sum('quantity'),
                    'sequence_no' => $group->first()['sequence_no'],
                ];
            })
            ->sortBy('sequence_no')
            ->values()
            ->toArray();
    }

    // create a function that gets all the inbounds that has delivery receipt
    public static function getInboundsWithDeliveryReceipt($branchCode)
    {
        return Inbound::where('branch_code', $branchCode)
            ->has('deliveryReceipt')
            ->get();
    }
}
