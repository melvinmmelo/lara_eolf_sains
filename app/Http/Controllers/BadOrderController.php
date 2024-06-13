<?php

namespace App\Http\Controllers;

use App\Models\BadOrder;
use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BadOrderController extends Controller
{
    // public function index()
    // {
    //     $badOrders = BadOrder::all(); // Retrieve all bad orders from the database
    
    //     return view('badorder', compact('badOrders'));

    // }

    public function index()
    {
        $badOrders = BadOrder::with('customer')->get();
    
        // Group by bo_id and summarize the total amount
        $summarizedBadOrders = $badOrders->groupBy('bo_id')->map(function ($group) {
            return [
                'bo_id' => $group->first()->bo_id,
                'customer' => $group->first()->customer,
                'storeinfo' => $group->first()->customer->storeinfo,
                'created_at' => $group->first()->created_at,
                'amount' => $group->sum('amount'),
                'remarks' => $group->first()->remarks,
            ];
        });
    
        return view('badorder', ['badOrders' => $summarizedBadOrders]);
    }

}


