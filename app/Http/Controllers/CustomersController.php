<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomersController extends Controller
{
    //
    public function index(){
        return view("customers");
    }


    public function create(){
        return view("create-customer");
    }
}
