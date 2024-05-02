<?php

namespace App\Http\Controllers;

use App\Models\EquipmentStore; // Import the EquipmentStore model
use App\Models\Equipment; // Import the Equipment model
use Illuminate\Http\Request;


class EquipmentStoreController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    

    public function index(Request $request)
    {
        // Retrieve customer_id and store_id from the request
        $customer_id = $request->input('customer_id');
        $store_id = $request->input('store_id');

        // Retrieve all equipment store entries for the specified customer and store
        $equipments = EquipmentStore::where('customer_id', $customer_id)
                                    ->where('store_id', $store_id)
                                    ->get();

        // Retrieve available equipment from the equipment table
        $availableEquipments = Equipment::where('status', 'available')->get();

        // Get the IDs of equipment already added to equipment_store for the specified customer and store
        $selectedEquipmentIds = $equipments->pluck('equipment_id')->toArray();

        // Filter out the selected equipment from the list of available equipment
        $availableEquipments = $availableEquipments->reject(function ($equipment) use ($selectedEquipmentIds) {
            return in_array($equipment->id, $selectedEquipmentIds);
        });

        // Pass the data to the view
        return view('equipment-store', compact('equipments', 'availableEquipments'));
    }

        



    /**
     * Store a newly created resource in storage.   
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    

     public function store(Request $request)
     {
         // Validate the incoming request data
         $request->validate([
             'customer_id' => 'required|exists:customers,id',
             'store_id' => 'required|exists:storeinfo,id',
             'equipment_id.*' => 'required|exists:equipment,id',
             'equipment_name.*' => 'required|string',
             'pull_status.*' => 'required|string',
         ]);
     
         // Get the data from the request
         $customer_id = $request->customer_id;
         $store_id = $request->store_id;
         $equipment_ids = $request->equipment_id;
         $pull_statuses = $request->pull_status;
     
         // Iterate over each equipment data
         foreach ($equipment_ids as $key => $equipment_id) {
             // Fetch the equipment from the equipment table based on the equipment_id
             $equipment = Equipment::findOrFail($equipment_id);
     
             // Create a new EquipmentStore instance
             $equipmentStore = new EquipmentStore();
             $equipmentStore->customer_id = $customer_id;
             $equipmentStore->store_id = $store_id;
             $equipmentStore->equipment_id = $equipment_id;
             $equipmentStore->serial = $equipment->serial_no; // Assign the serial from the equipment table
             $equipmentStore->type = $equipment->type; // Assign the type from the equipment table
             $equipmentStore->brand = $equipment->brand; // Assign the brand from the equipment table
             $equipmentStore->owned = $equipment->ownership; // Assign the owned from the equipment table
             $equipmentStore->pull_status = 'no';
             $equipmentStore->save();
         }
                     // Update the status of the equipment in the Equipment table
                     $equipment->status = 'added';
                     $equipment->save();
         // Redirect back with a success message
         return redirect()->back()->with('success', 'Equipment added successfully.');
     }

    
    /**
     * Remove the specified equipment store entry from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Retrieve the equipment store entry by its ID
        $equipmentStore = EquipmentStore::findOrFail($id);
        // dd($request->all());
        // Delete the equipment store entry
        $equipmentStore->delete();
    
        // Retrieve the equipment by its ID from the request
        $equipmentId = $request->input('equipment_id');
        $equipment = Equipment::findOrFail($equipmentId);
    
        // Update the status of the equipment to "available"
        $equipment->status = 'available';
        $equipment->save();
    
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Equipment store entry deleted successfully.');
    }
    

    

}