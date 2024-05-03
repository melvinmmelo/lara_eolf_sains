<?php

use App\Models\EquipmentStore;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(
    function () {

        Route::get('/get-equipmentcustomerstore/{id}', function ($id) {
            $equipment = EquipmentStore::find($id);
            echo $equipment->store->customer->fullName;
        });
    }
);
