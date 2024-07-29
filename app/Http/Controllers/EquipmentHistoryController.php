<?php

namespace App\Http\Controllers;

use App\Models\EquipmentHistory;
use Illuminate\Http\Request;

class EquipmentHistoryController extends Controller
{


    public function equipmentHistory(Request $request, $dno)
    {
        $equipments = EquipmentHistory::where('degic_no', $dno)->get();
        return view('equipment-history', compact('dno','equipments'));
    }
}
