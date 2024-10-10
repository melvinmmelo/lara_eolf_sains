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

    public static function getTotalOfInboundProducts($inboundId){
        $inbound = Inbound::find($inboundId);
        $products = json_decode($inbound->products, true);
        $total = 0;

        if($products){
            foreach($products as $product){
                $total += $product['quantity'] * $product['price'];
            }
        }
        return $total;
    }

    public static function getTotalOfBadOrderOfInboundId($inboundId)
    {

        $inbound = BadOrder::ofInboundId($inboundId)->get();

        if($inbound){

            $total = 0;
            foreach($inbound as $badOrder){
                $total += $badOrder->amount;
            }
            return $total;
        }
        return 0;
    }

    // per date
    // default is today
    public static function getTotalOfAllInboundProducts($branchCode, $fromDate = null, $toDate = null){

        if($fromDate && $toDate){
            $fromDate = date('Y-m-d', strtotime($fromDate));
            $toDate = date('Y-m-d', strtotime($toDate));

            $inbounds = Inbound::where('branch_code', $branchCode)
            ->whereBetween('order_date', [$fromDate, $toDate])->whereNotIn('status', ['Cancelled', 'Wrong entry', 'Deleted'])
            ->get();

        }else{

            $dateToExtract = date('Y-m-d');


            $inbounds = Inbound::where('branch_code', $branchCode)
            ->whereDate('order_date', $dateToExtract)->whereNotIn('status', ['Cancelled', 'Wrong entry', 'Deleted'])
            ->get();

        }





        $products = $inbounds->flatMap(function ($inbound) {
            $inboundProducts = json_decode($inbound->products, true);
            return collect($inboundProducts)
                ->map(function ($product) use ($inbound) {
                    $productType = ProductType::code($product['ptype_code'])->first();
                    return [
                        'code' => $product['code'],
                        'quantity' => $product['quantity'],
                        'sequence_no' => $productType->sequence_no,
                    ];
                });
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

        return $products;
    }

    public static function getTotalOfAllInboundProductsv2($branchCode) // ! PANG OVERALL
    {

        $inbounds = Inbound::where('branch_code', $branchCode)->whereNotIn('status', ['Cancelled', 'Wrong entry', 'Deleted'])->get();
        // $inbounds = Inbound::where('branch_code', $branchCode)->whereBetween('order_date', ['2024-08-19', '2024-08-21'])->get(); // ! uncomment if you want to filter by range of dates
        $products = $inbounds->flatMap(function ($inbound) {
            $inboundProducts = json_decode($inbound->products, true);
            return collect($inboundProducts)
                ->map(function ($product) use ($inbound) {
                    $productType = ProductType::code($product['ptype_code'])->first();
                    return [
                        'code' => $product['code'],
                        'quantity' => $product['quantity'],
                        'sequence_no' => $productType->sequence_no,
                    ];
                });
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

        return $products;
    }

    // create a function that gets all the inbounds that has delivery receipt
    public static function getInboundsWithDeliveryReceipt($branchCode)
    {
        return Inbound::where('branch_code', $branchCode)
            ->has('deliveryReceipt')
            ->get();
    }


}
