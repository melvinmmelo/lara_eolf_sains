<?php

namespace App\Http\Controllers;

use App\Models\EquipmentStore; // Import the EquipmentStore model
use App\Models\Equipment; // Import the Equipment model
use App\Models\Customers as Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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
        $equipments = EquipmentStore::with('storeinfo')
            ->where('customer_id', $customer_id)
            ->where('store_id', $store_id)
            ->get();
        //dd($equipments);

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
        // return view('customers', compact('availableEquipments'));
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

            $equipment = Equipment::findOrFail($equipment_id);
            $equipmentStore = new EquipmentStore();
            $equipmentStore->customer_id = $customer_id;
            $equipmentStore->store_id = $store_id;
            $equipmentStore->equipment_id = $equipment_id;
            $equipmentStore->serial = $equipment->serial_no;
            $equipmentStore->type = $equipment->type;
            $equipmentStore->brand = $equipment->brand;
            $equipmentStore->owned = $equipment->ownership;
            $equipmentStore->pull_status = 'no';
            $equipmentStore->save();


            $equipment->status = 'available';
            $equipment->save();
        }


        activity('equipment-store')
            ->withProperties(['customer_id' => $customer_id, 'store_id' => $store_id, 'equipment_ids' => $equipment_ids, 'pull_statuses' => $pull_statuses])
            ->log('equipment added to store');

        return redirect()->back()->with('success', 'Equipment added successfully.');
        // return Redirect::route('customers')->with('success', 'Equipment added successfully.');
    }


    /**
     * Remove the specified equipment store entry from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $equipmentStore = EquipmentStore::findOrFail($id);
        $equipmentStore->delete();

        $equipmentId = $request->input('equipment_id');
        $equipment = Equipment::findOrFail($equipmentId);

        // Update the status of the equipment to "available"
        $equipment->status = 'Active';
        $equipment->save();

        activity('equipment-store')
            ->withProperties(['equipment_store_id' => $id, 'equipment_id' => $equipmentId])
            ->log('equipment store entry deleted');

        return redirect()->back()->with('success', 'Equipment store entry deleted successfully.');
    }



    public function updatePullStatus(Request $request)
    {
        $activityLog  = 'pullOut';

        $successMsg = 'Equipment pulled out successfully.';

        $request->validate([
            'pull_equipment_id' => 'required|exists:equipment,id',
            'replace_equipment_id.*' => 'required|exists:equipment,id',
            'remarks' => 'required|string|max:255', // Validate the remarks field
            'customer_id' => 'required|exists:customers,id',
            'store_id' => 'required|exists:storeinfo,id',
        ]);

        $pullEquipmentId = $request->input('pull_equipment_id');
        $remarks = $request->input('remarks');

        $equipmentStore = EquipmentStore::with('storeinfo', 'customer')->where('equipment_id', $pullEquipmentId)->firstOrFail();

        $esName = $equipmentStore->storeinfo->storename;
        $customerName = $equipmentStore->customer->fullName;

        // $equipmentStore->pull_status = 'yes';
        // $equipmentStore->remarks = $remarks;
        // $equipmentStore->save();

        $equipment = Equipment::findOrFail($pullEquipmentId);
        $equipment->status = 'Active';
        $equipment->save();

        $replaceEquipmentIds = [];

        if ($request->has('replace_equipment_id')) {

            $activityLog = 'pullOut-replace';
            $successMsg = 'Equipment pulled out and replaced successfully.';

            $replaceEquipmentIds = $request->input('replace_equipment_id');
            $customer_id = $request->input('customer_id');
            $store_id = $request->input('store_id');

            foreach ($replaceEquipmentIds as $replaceEquipmentId) {
                $newEquipment = Equipment::findOrFail($replaceEquipmentId);

                $newEquipmentStore = new EquipmentStore();
                $newEquipmentStore->customer_id = $customer_id;
                $newEquipmentStore->store_id = $store_id;
                $newEquipmentStore->equipment_id = $replaceEquipmentId;
                $newEquipmentStore->serial = $newEquipment->serial_no;
                $newEquipmentStore->type = $newEquipment->type;
                $newEquipmentStore->brand = $newEquipment->brand;
                $newEquipmentStore->owned = $newEquipment->ownership;
                $newEquipmentStore->pull_status = 'no';
                $newEquipmentStore->save();

                // Update the status of the new equipment in the Equipment table
                $newEquipment->status = 'available';
                $newEquipment->save();
            }
        }

        activity('manage-equipment-store')
            ->withProperties(['customer' => $customerName, 'store' => $esName, 'equipment' => $equipment->code, 'pull_equipment_id' => $pullEquipmentId, 'replace_equipment_ids' => $replaceEquipmentIds, 'remarks' => $remarks])
            ->log($activityLog);

        $equipmentStore->delete();

        return redirect()->back()->with('success', $successMsg);
    }
}
