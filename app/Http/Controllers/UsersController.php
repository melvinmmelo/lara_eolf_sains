<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users', compact('users'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'integer|exists:users,id',
            'e_lname' => 'required|string|max:190',
            'e_fname' => 'required|string|max:190',
            'e_cno' => 'string|max:190',
            'c_addr' => 'string|max:190',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->last_name = $request->e_lname;
        $user->first_name = $request->e_fname;
        $user->contact_no = $request->e_cno;
        $user->address = $request->c_addr;
        $user->save();

        return redirect()->back()->with('sucess', 'Data saved!');

    }

    public function deliveryPersons()
    {
        return view('delivery-persons');
    }
}
