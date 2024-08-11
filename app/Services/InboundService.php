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

    // check if inbound is existing in bad order table
    public static function isWithBadOrder($inboundId)
    {
        // $badOrder = BadOrder::where('inbound_id', $inboundId)->first();
        // if($badOrder){
        //     return true;
        // }
        return false;
    }

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

    public static function getTotalOfAllInboundProducts($branchCode, $dateToExtract = null){

        if($dateToExtract){
            $dateToExtract = date('Y-m-d', strtotime($dateToExtract));
        }else{
            $dateToExtract = date('Y-m-d');
        }

        $inbounds = Inbound::where('branch_code', $branchCode)
            ->whereDate('order_date', $dateToExtract)
            ->get();

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
