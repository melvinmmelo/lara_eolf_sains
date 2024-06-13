<?php

namespace App\Http\Controllers;

use App\Models\CompanyDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyDetailsController extends Controller
{
    protected static $companyInitialId = 1;

    // view company details
    public function index(){
        $company = CompanyDetails::find(self::$companyInitialId);
        return view('company', compact('company'));
    }

    // edit view company details
    public function edit(){
        $company = CompanyDetails::find(self::$companyInitialId);
        return view('company', compact('company'));
    }

    public function update(CompanyDetails $companyDetails, Request $request){
        // dd($request->all());
        $logoPath = '';

        $vData = $request->validate([
            'name' => 'required|max:199',
            'contact_no' => 'nullable|max:199',
            'email' => 'nullable|max:199',
            'address' => 'nullable|max:199',
            'logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $companyDetails->name = $vData['name'];
        $companyDetails->contact_no = $vData['contact_no'];
        $companyDetails->email = $vData['email'];
        $companyDetails->address = $vData['address'];
        $companyDetails->logo = $logoPath;
        $companyDetails->save();

        activity()->log("User updates the company details.");

        return redirect()->back()->with('success', 'Company details saved!');
    }
}
