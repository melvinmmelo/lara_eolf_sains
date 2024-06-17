<?php

namespace App\Services;

use App\Models\BadOrder;
use App\Models\Inbound;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboundService extends Model
{
    use HasFactory;

    // check if inbound is existing in bad order table
    public static function isWithBadOrder($inboundId)
    {
        $badOrder = BadOrder::where('inbound_id', $inboundId)->first();
        if($badOrder){
            return true;
        }
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

}
