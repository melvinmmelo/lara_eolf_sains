<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Branches;


class BranchesController extends Controller
{
    //
    public function setBranchSession($code)
    {
        session(['branch_code' => $code]);
        return redirect()->back()->with('success', 'Branch set successfully!');
    }

    public function index(): View
    {
        // Fetch all vehicles from the database
        $branches = Branches::all();
        // Pass the vehicles data to the view
        return view('branch', compact('branches'));
    }

    public function store(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'address' => 'required',
            'office_no' => 'required',

            // Add more validation rules as needed
        ]);
        //$status = 'NOT AVAILABLE';

        // // Check if the request data is 'on'
        // if ($request->status === 'on') {
        //     $status = 'AVAILABLE';
        // }
        Branches::create([
            'code' => $request->code,
            'name' => $request->name,
            'address' => $request->address,
            'office_no' => $request->office_no,

            // Add more fields as needed
        ]);

        return redirect('/branch/')->with('success', 'Branch added successfully!');
    }
}
