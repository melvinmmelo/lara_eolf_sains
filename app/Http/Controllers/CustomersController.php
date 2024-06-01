<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers as Customer;
use App\Models\PhAddr; // Adjust the namespace and model accordingly
use App\Models\StoreInfo;
use App\Models\EquipmentStore; // Import the EquipmentStore model
use App\Models\Equipment; // Import the Equipment model

class CustomersController extends Controller
{

    public function index()
    {
        // Fetch all customers from the database
        // $customers = Customer::all();

        // $customers = Customer::with('storeinfo')->get();
        // $customers = Customer::with(['storeinfo', 'equipmentStores'])->get();
        $customers = Customer::with(['stores.equipmentStores'])->get();
        
        return view('customersinfo', compact('customers'));

    }


    public function create()
    {
        // $customers = Customer::all();
        // dd($customers);
        return view('create-customer', compact('customers'));

    }



    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'branch_code' => 'required',
            'lastname' => 'required',
            'firstname' => 'required',
            'companyname' => 'required',
            'middlename' => 'required',
            'storename' => 'required',

            // Add more validation rules as needed
        ]);

        // Retrieve the names of the region, province, city, etc. based on their codes
        $regionName = PhAddr::where('code', $request->region)->value('name');
        $provinceName = PhAddr::where('code', $request->province)->value('name');
        $cityName = PhAddr::where('code', $request->city)->value('name');
        $brgyName = PhAddr::where('code', $request->brgy)->value('name');

        $regionName2 = PhAddr::where('code', $request->region2)->value('name');
        $provinceName2 = PhAddr::where('code', $request->province2)->value('name');
        $cityName2 = PhAddr::where('code', $request->city2)->value('name');
        $brgyName2 = PhAddr::where('code', $request->brgy2)->value('name');
        // Create a new Customer instance with the provided data
        $customer = Customer::create([
            'branch_code' => $request->branch_code,
            'distributor' => $request->distributor,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'companyname' => $request->companyname,
            'contact_no' => $request->contact_no,
            'email' => $request->email,
            'tin' => $request->tin,
            'region' => $regionName, // Insert region name instead of code
            'province' => $provinceName, // Insert province name instead of code
            'city' => $cityName, // Insert city name instead of code
            'brgy' => $brgyName, // Insert barangay name instead of code
            'subdivision' => $request->subdivision,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            // Add more fields as needed
        ]);

        // Create a new store info record related to the customer
        $storeInfo = StoreInfo::create([
            'customer_id' => $customer->id,
            'storename' => $request->storename,
            'contactno' => $request->contactno2,
            'region' => $regionName2, // Insert region name instead of code
            'province' => $provinceName2, // Insert province name instead of code
            'city' => $cityName2, // Insert city name instead of code
            'brgy' => $brgyName2, // Insert barangay name instead of code
            'subdivision' => $request->subdivision2,
            'latitude' => $request->latitude2,
            'longitude' => $request->longitude2,
            'listype' => $request->listype,
            'length_stay' => $request->length_stay,
            'remarks' => $request->remarks,
        ]);

        // Redirect the user back with a success message
        return redirect('/customers')->with('success', 'Customer added successfully!');
    }


    // public function edit($id)
    // {
    //     $customer = Customer::findOrFail($id);
    //     return view('edit_customer', compact('customer'));
    // }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request)
{
    // dd($request->all());
    $customer = Customer::findOrFail($request->id);
    $storeInfo = $customer->storeinfo;

    // If store info doesn't exist, create a new one
    if (!$storeInfo) {
        $storeInfo = new StoreInfo();
        $storeInfo->customer_id = $customer->id; // Link the new StoreInfo to the customer
    }

    $request->validate([
        'lastname' => 'required',
        'firstname' => 'required',
        'companyname' => 'required',
        // Add more validation rules as needed
    ]);

    // Retrieve the names of the region, province, city, etc. based on their codes
    // $regionName = PhAddr::where('code', $request->e_region)->value('name');
    // $provinceName = PhAddr::where('code', $request->e_province)->value('name');
    // $cityName = PhAddr::where('code', $request->e_city)->value('name');
    // $brgyName = PhAddr::where('code', $request->e_brgy)->value('name');

    // $regionName2 = PhAddr::where('code', $request->e_region2)->value('name');
    // $provinceName2 = PhAddr::where('code', $request->e_province2)->value('name');
    // $cityName2 = PhAddr::where('code', $request->e_city2)->value('name');
    // $brgyName2 = PhAddr::where('code', $request->e_brgy2)->value('name');

    $customer->update([
        'distributor' => $request->distributor,
        'lastname' => $request->lastname,
        'firstname' => $request->firstname,
        'middlename' => $request->middlename,
        'companyname' => $request->companyname,
        'contact_no' => $request->contact_no,
        'email' => $request->email,
        'tin' => $request->tin,
        'region' => $request->e_region, // Insert region name instead of code
        'province' => $request->e_province, // Insert province name instead of code
        'city' => $request->e_city, // Insert city name instead of code
        'brgy' => $request->e_brgy, // Insert barangay name instead of code
        'subdivision' => $request->subdivision,
        'longitude' => $request->longitude,
        'latitude' => $request->latitude,
    ]);

    $storeInfo->storename = $request->storename;
    $storeInfo->contactno = $request->contactno2;
    $storeInfo->region = $request->e_region2; // Insert region name instead of code
    $storeInfo->province = $request->e_province2; // Insert province name instead of code
    $storeInfo->city = $request->e_city2; // Insert city name instead of code
    $storeInfo->brgy = $request->e_brgy2; // Insert barangay name instead of code
    $storeInfo->subdivision = $request->subdivision2;
    $storeInfo->latitude = $request->latitude2;
    $storeInfo->longitude = $request->longitude2;
    $storeInfo->listype = $request->listype;
    $storeInfo->length_stay = $request->length_stay;
    $storeInfo->remarks = $request->remarks;

    // Save the store info (new or existing)
    $storeInfo->save();

    return redirect('/customers/')->with('success', 'Customer updated successfully!');
}


    // public function destroy($id)
    // {
    //     $customer = Customer::findOrFail($id);
    //     $customer->delete();
    //     return redirect('/customers/')->with('success', 'Customer deleted successfully!');
    // }


    // public function destroyStore($customerId, $storeId)
    // {
    //     $store = Storeinfo::findOrFail($storeId);
    //     $store->delete();

    //     $customer = Customer::findOrFail($customerId);
    //     if ($customer->stores()->count() == 0) {
    //         $customer->delete();
    //     }

    //     return redirect('/customers/')->with('success', 'Store and possibly customer deleted successfully!');
    // }
    public function destroyStore($customerId, $storeId, Request $request)
    {
        $store = Storeinfo::findOrFail($storeId);

        // Update the status of all equipment associated with this store
        foreach ($request->input('equipment_ids', []) as $equipmentId) {
            $equipment = Equipment::findOrFail($equipmentId);
            $equipment->status = 'available';
            $equipment->save();
        }

        $store->delete();

        $customer = Customer::findOrFail($customerId);
        if ($customer->stores()->count() == 0) {
            $customer->delete();
        }

        return redirect('/customers/')->with('success', 'Store and possibly customer deleted successfully, and equipment status updated to available!');
    }
}
