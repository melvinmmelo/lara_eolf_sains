<?php

namespace App\Http\Controllers;

use App\Models\CompanyDetails;
use Illuminate\Http\Request;

class CompanyDetailsController extends Controller
{
    protected static $companyInitialId = 1;

    // view company details
    public function index(){
        $company = CompanyDetails::find(self::$companyInitialId);
        return view('company', ['company' => $company]);
    }

    // edit view company details
    public function edit(){
        $company = CompanyDetails::find(self::$companyInitialId);
        return view('company', ['company' => $company]);
    }
}
