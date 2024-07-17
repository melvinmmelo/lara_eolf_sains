<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers as Customer;
use App\Models\PhAddr;
use App\Models\StoreInfo;
use App\Models\Equipment;

class CustomersController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['stores.equipmentStores'])->branchCode(session('branch_code'))->get();
        return view('customersinfo', compact('customers'));
    }

    public function create()
    {
        return view('create-customer', compact('customers'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'branch_code' => 'required',
            'lastname' => 'required',
            'firstname' => 'required',
            'companyname' => 'required',
            'middlename' => 'nullable',
            'storename' => 'required',
        ]);

        $regionName = PhAddr::where('code', $request->region)->value('name');
        $provinceName = PhAddr::where('code', $request->province)->value('name');
        $cityName = PhAddr::where('code', $request->city)->value('name');
        $brgyName = PhAddr::where('code', $request->brgy)->value('name');

        $regionName2 = PhAddr::where('code', $request->region2)->value('name');
        $provinceName2 = PhAddr::where('code', $request->province2)->value('name');
        $cityName2 = PhAddr::where('code', $request->city2)->value('name');
        $brgyName2 = PhAddr::where('code', $request->brgy2)->value('name');


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
        ]);

        StoreInfo::create([
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

        return redirect('/customers')->with('success', 'Customer added successfully!');
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request)
    {
        $customer = Customer::findOrFail($request->id);
        $storeInfo = $customer->storeinfo;

        if (!$storeInfo) {
            $storeInfo = new StoreInfo();
            $storeInfo->customer_id = $customer->id; // Link the new StoreInfo to the customer
        }

        $request->validate([
            'lastname' => 'required',
            'firstname' => 'required',
            'companyname' => 'required',
            'status' => 'required',
        ]);

        $customer->update([
            'distributor' => $request->distributor,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'companyname' => $request->companyname,
            'contact_no' => $request->contact_no,
            'email' => $request->email,
            'tin' => $request->tin,
            'region' => $request->e_region,
            'province' => $request->e_province,
            'city' => $request->e_city,
            'brgy' => $request->e_brgy,
            'subdivision' => $request->subdivision,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'status' => $request->status,
        ]);

        $storeInfo->storename = $request->storename;
        $storeInfo->contactno = $request->contactno2;
        $storeInfo->region = $request->e_region2;
        $storeInfo->province = $request->e_province2;
        $storeInfo->city = $request->e_city2;
        $storeInfo->brgy = $request->e_brgy2;
        $storeInfo->subdivision = $request->subdivision2;
        $storeInfo->latitude = $request->latitude2;
        $storeInfo->longitude = $request->longitude2;
        $storeInfo->listype = $request->listype;
        $storeInfo->length_stay = $request->length_stay;
        $storeInfo->remarks = $request->remarks;

        $storeInfo->save();

        return redirect('/customers/')->with('success', 'Customer updated successfully!');
    }

    public function destroyStore($customerId, $storeId, Request $request)
    {
        $store = Storeinfo::findOrFail($storeId);

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
