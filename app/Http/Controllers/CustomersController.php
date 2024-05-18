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

        $customers = Customer::with('storeinfo')->get();

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
        $customer = Customer::findOrFail($request->id);
        // $customer = Customer::findOrFail($id);
        $storeInfo = $customer->storeinfo;
        $request->validate([
            'lastname' => 'required',
            'firstname' => 'required',
            'companyname' => 'required',
            // Add more validation rules as needed
        ]);
        // Retrieve the names of the region, province, city, etc. based on their codes
        $regionName = PhAddr::where('code', $request->region)->value('name');
        $provinceName = PhAddr::where('code', $request->province)->value('name');
        $cityName = PhAddr::where('code', $request->city)->value('name');
        $brgyName = PhAddr::where('code', $request->brgy)->value('name');

        $customer->update([
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
        ]);

       $storeInfo->update([
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
        return redirect('/customers/')->with('success', 'Customer updated successfully!');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect('/customers/')->with('success', 'Customer deleted successfully!');
    }

}
