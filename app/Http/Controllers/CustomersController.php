<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers as Customer;

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

        // dd($request->all());
        $request->validate([
            'lastname' => 'required',
            'firstname' => 'required',
            'companyname' => 'required',

            // Add more validation rules as needed
        ]);

        Customer::create([
            'distributor' => $request->distributor,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'companyname' => $request->companyname,
            'contact_no' => $request->contact_no,
            'email' => $request->email,
            'tin' => $request->tin,
            'region' => $request->region,
            'province' => $request->province,
            'city' => $request->city,
            'brgy' => $request->brgy,
            'subdivision' => $request->subdivision,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            // Add more fields as needed
        ]);

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

        $customer->update([
            'distributor' => $request->distributor,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'companyname' => $request->companyname,
            'contact_no' => $request->contact_no,
            'email' => $request->email,
            'tin' => $request->tin,
            'region' => $request->region,
            'province' => $request->province,
            'city' => $request->city,
            'brgy' => $request->brgy,
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
