<?php

// app/Http/Controllers/PhAddrController.php

namespace App\Http\Controllers;

use App\Models\PhAddr; // Adjust the namespace and model accordingly
use Illuminate\Http\Request;

class PhAddrController extends Controller
{
    public function getRegions()
    {
        $regions = PhAddr::where('g_level', 'Reg')->get();
        // return response()->json($regions);
        // $regions = PhAddr::where('g_Level', 'Reg')->get(['id', 'name']);
        // dd($regions); // Add this line to debug
        return response()->json($regions);
    }

    // public function getProvinces($regionId)
    // {
    //     $provinces = PhAddr::where('g_level', 'Prov')
    //                        ->where('code', 'like', $regionId.'%')
    //                        ->get();
    //     return response()->json($provinces);
    // }

    public function getProvinces($regionId)
    {
        
        $regionCodePrefix = substr($regionId, 0, 2);
    
        
        $provinceCodePattern = $regionCodePrefix . '%';
        // dd($regionCodePrefix, $provinceCodePattern);
        
        $provinces = PhAddr::where('g_level', 'Prov')
                           ->where('code', 'like', $provinceCodePattern)
                           ->get();
    
        return response()->json($provinces);
    }
    

    public function getCities($provinceId)
    {

        $provinceCodePrefix = substr($provinceId, 0, 4);
    
        
        $cityCodePattern = $provinceCodePrefix . '%';

        $cities = PhAddr::where('g_level', 'Mun')
                        ->where('code', 'like', $cityCodePattern)
                        ->get();
        return response()->json($cities);
    }

    public function getBrgy($cityId)
    {

        $cityCodePrefix = substr($cityId, 0, 6);
    
        
        $brgyCodePattern = $cityCodePrefix . '%';

        $brgy = PhAddr::where('g_level', 'Bgy')
                        ->where('code', 'like', $brgyCodePattern)
                        ->get();
        return response()->json($brgy);
    }
}
