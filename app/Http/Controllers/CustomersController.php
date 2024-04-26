<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers as Customer;
use App\Models\PhAddr; // Adjust the namespace and model accordingly


class CustomersController extends Controller
{

    public function index()
    {
        // Fetch all customers from the database
        $customers = Customer::all();
        // Pass the customers data to the view
        return view('customers', compact('customers'));
        
    }


    public function create()
    {
        $customers = Customer::all();
        // dd($customers);
        return view('create-customer', compact('customers'));

    }



    public function store(Request $request)
    {
        // Validate the incoming request data
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
    
        // Create a new Customer instance with the provided data
        Customer::create([
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
    
        // Redirect the user back with a success message
        return redirect('/customers/')->with('success', 'Customer added successfully!');
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

        return redirect('/customers/')->with('success', 'Customer updated successfully!');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect('/customers/')->with('success', 'Customer deleted successfully!');
    }

}
