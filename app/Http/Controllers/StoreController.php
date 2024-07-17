<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreInfo; // Make sure to import the StoreInfo model
use App\Models\PhAddr; // Adjust the namespace and model accordingly

class StoreController extends Controller
{
    // public function index()
    // {

    //     // Fetch all storeinfo records
    //     $storeinfos = StoreInfo::all();

    //     // Pass the storeinfo data to the view
    //     return view('store-info', compact('storeinfos'));
    // }

    public function index(Request $request)
    {
        $customer_id = $request->query('customer_id');

        // Fetch store information based on customer_id
        $storeinfos = StoreInfo::where('customer_id', $customer_id)->get();

        return view('store-info', compact('storeinfos'));
    }

    public function store(Request $request)
    {



        // dd($request->all());
        $request->validate([
            'storename' => 'required',
            'contactno' => 'required',
        ]);

        $regionName = PhAddr::where('code', $request->region)->value('name');
        $provinceName = PhAddr::where('code', $request->province)->value('name');
        $cityName = PhAddr::where('code', $request->city)->value('name');
        $brgyName = PhAddr::where('code', $request->brgy)->value('name');
        Storeinfo::create([
            'customer_id' => $request->customer_id,
            'storename' => $request->storename,
            'contactno' => $request->contactno,
            'region' => $regionName,
            'province' => $provinceName,
            'city' => $cityName,
            'brgy' => $brgyName,
            'subdivision' => $request->subdivision,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'listype' => $request->listype,
            'length_stay' => $request->length_stay,
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()
            ->with('success', 'Store Info added successfully!');


    }
    public function update(Request $request)
    {
        $storeInfo = StoreInfo::findOrFail($request->id);

        $request->validate([
            'storename' => 'required',
            // Add more validation rules as needed
        ]);
        $regionName = PhAddr::where('code', $request->region)->value('name');
        $provinceName = PhAddr::where('code', $request->province)->value('name');
        $cityName = PhAddr::where('code', $request->city)->value('name');
        $brgyName = PhAddr::where('code', $request->brgy)->value('name');
        $storeInfo->update([
            'customer_id' => $request->customer_id,
            'storename' => $request->storename,
            'contactno' => $request->contactno,
            'region' => $regionName, // Insert region name instead of code
            'province' => $provinceName, // Insert province name instead of code
            'city' => $cityName, // Insert city name instead of code
            'brgy' => $brgyName, // Insert barangay name instead of code
            'subdivision' => $request->subdivision,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'listype' => $request->listype,
            'length_stay' => $request->length_stay,
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Store Info updated successfully!');
    }

    // public function destroy($id)
    // {
    //     $customer = store::findOrFail($id);
    //     $customer->delete();
    //     return redirect()->route('store-info.index')->with('success', 'Store Info deleted successfully!')
    //         ->withQuery(['customer_id' => $request->customer_id, 'customer_name' => $request->customer_name]);
    // }
    public function destroy($id, Request $request)
    {
        $storeInfo = StoreInfo::findOrFail($id); // Correct the model name to 'StoreInfo'
        $storeInfo->delete();
        return redirect()->back()
            ->with('success', 'Store Info deleted successfully!');

    }
}
