<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function index()
    {
        // Fetch all customers from the database
        $customers = Customer::all();
        
        // Pass the customers data to the view
        return view('customer.index', compact('customers'));
    }

    public function create()
    {
        return view('customer.create');
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'lastname' => 'required',
            'firstname' => 'required',
            'companyname' => 'required',
            'email' => 'required|email',
            // Add more validation rules as needed
        ]);

        CustomerTest::create([
            'distributor' => $request->distributor,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'companyname' => $request->companyname,
            'email' => $request->email,
            'tin' => $request->tin,
            'region' => $request->region,
            'province' => $request->province,
            'city' => $request->city,
            'brgy' => $request->brgy,
            'subdivision' => $request->subdivision,
            'longitute' => $request->longitute,
            'latitude' => $request->latitude,
            // Add more fields as needed
        ]);
        
        return redirect('/customer')->with('success', 'Customer added successfully!');
    }

    public function edit($id)
    {
        $customer = CustomerTest::findOrFail($id);
        return view('customer.edit', compact('customer'));
    }
    public function update(Request $request, $id)
    {
        $customer = CustomerTest::findOrFail($id);
        
        $request->validate([
            'lastname' => 'required',
            'firstname' => 'required',
            'companyname' => 'required',
            'email' => 'required|email',
            // Add more validation rules as needed
        ]);
    
        $customer->update([
            'distributor' => $request->distributor,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'companyname' => $request->companyname,
            'email' => $request->email,
            'tin' => $request->tin,
            'region' => $request->region,
            'province' => $request->province,
            'city' => $request->city,
            'brgy' => $request->brgy,
            'subdivision' => $request->subdivision,
            'longitute' => $request->longitute,
            'latitude' => $request->latitude,
        ]);
    
        return redirect('/customer')->with('success', 'Customer updated successfully!');
    }

    public function destroy($id)
    {
        $customer = CustomerTest::findOrFail($id);
        $customer->delete();
        return redirect('/customer')->with('deleted', 'Customer deleted successfully!');
    }

}
