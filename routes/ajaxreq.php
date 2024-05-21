<?php

use App\Models\EquipmentStore;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(
    function () {

        Route::get('/get-equipmentcustomerstore/{id}', function ($id) {
            $equipment = EquipmentStore::find($id);

            // return json  response
            return response()->json(['customer_name' => $equipment->store->customer->fullName, 'customer_id' => $equipment->store->customer->id]);

            // echo $equipment->store->customer->fullName;
        });
    }
);
