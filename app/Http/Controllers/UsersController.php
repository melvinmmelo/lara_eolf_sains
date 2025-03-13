<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role as ModelsRole;

class UsersController extends Controller
{

    public function reset(Request $request)
    {
        $request->validate([
            'ruser_id' => 'required|integer|exists:users,id',
            'password' => 'required|string|min:8',
        ]);

        $user = User::findOrFail($request->ruser_id);
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password reset!');
    }

    public function index()
    {
        $users = User::all();
        $roles = ModelsRole::all();

        return view('users', compact('users', 'roles'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'integer|exists:users,id',
            'e_lname' => 'required|max:190',
            'e_fname' => 'required|max:190',
            'e_cno' => 'nullable|max:190',
            'e_address' => 'nullable|max:190',
            'e_role' => 'required',
            'e_status' => 'required',
        ], [
            'user_id.integer' => 'The user ID must be a valid number.',
            'user_id.exists' => 'The selected user does not exist.',
            'e_lname.required' => 'The last name field is required.',
            'e_lname.max' => 'The last name may not be greater than 190 characters.',
            'e_fname.required' => 'The first name field is required.',
            'e_fname.max' => 'The first name may not be greater than 190 characters.',
            'e_cno.max' => 'The contact number may not be greater than 190 characters.',
            'e_address.max' => 'The address may not be greater than 190 characters.',
            'e_role.required' => 'The role field is required.',
            'e_status.required' => 'The status field is required.',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->last_name = $request->e_lname;
        $user->first_name = $request->e_fname;
        $user->contact_no = $request->e_cno;
        $user->address = $request->e_address;
        $user->status = $request->e_status;

        if ($request->e_role) {
            if (!$user->hasRole($request->e_role)) {
                $user->assignRole($request->e_role);
            }
        }

        $user->save();

        return redirect()->back()->with('sucess', 'Data saved!');

    }

    public function deliveryPersons()
    {
        return view('delivery-persons');
    }

    public function delete($id)
    {

        if (auth()->user()->hasRole('admin')) {

            return redirect()->back()->withErrors('Cannot reset your own password!');
        }


        $user = User::findOrFail($id);

        if($user->hasRole('admin')){
            return redirect()->back()->withErrors('Cannot delete admin!');
        }

        return redirect()->back()->with('sucess', 'Data deleted!');
    }

    public function resetPassword($id)
    {
        if(auth()->user()->hasRole('admin')){

            return redirect()->back()->withErrors('Cannot reset your own password!');
        }

        $user = User::findOrFail($id);
        $user->password = bcrypt('password');
        $user->save();

        return redirect()->back()->with('sucess', 'Password reset!');
    }
}
