<?php

namespace App\Http\Controllers;

use App\Models\EquipmentHistory;
use Illuminate\Http\Request;

class EquipmentHistoryController extends Controller
{


    public function equipmentHistory(Request $request, $eqsnos)
    {
        $equipments = EquipmentHistory::where('serial_no', $eqsnos)->get();
        return view('equipment-history', compact('equipments'));
    }
}
