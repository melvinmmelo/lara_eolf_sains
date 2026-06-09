<?php

use App\Models\EquipmentStore;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(
    function () {

        Route::get('/get-equipmentcustomerstore/{id}', function ($id) {
            $equipment = EquipmentStore::find($id);
            $customer = $equipment->store->customer;

            // return json  response
            return response()->json([
                'customer_name' => $customer->fullName,
                'customer_id' => $customer->id,
                // Customer's assigned price level, or the branch default (may be null).
                'pricelevel_id' => $customer->resolvedPriceLevelId(),
            ]);

            // echo $equipment->store->customer->fullName;
        });
    }
);
