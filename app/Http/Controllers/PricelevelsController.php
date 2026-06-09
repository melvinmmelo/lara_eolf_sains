<?php

namespace App\Http\Controllers;

use App\Models\pricelevels;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PricelevelsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pricelevels = pricelevels::branch(session('branch_code'))->where('pl_status', 'active')->get();
        return view('pricing-level', compact('pricelevels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_code' => 'required',
            'name' => 'required',
            'Description' => 'required',
            'branch_code' => 'required',
            'priceType' => 'required',
        ]);

        $status = 'ACTIVE';

        // switch($request->priceType){
        //     case 'BAD PRICING':
        //         $plName = 'BAD PRICING';
        //         break;
        //     case 'FACTORY PRICE':
        //         $plName = 'FACTORY PRICE';
        //         break;
        //     default:
        //         $plName = $request->name;
        // }

        // check if there is a pricing level with the same name
        $check = pricelevels::where('pl_name', $request->name)->where('branch_code', $request->branch_code)->first();
        if($check){
            return redirect('/pricing-level/')->with('error', 'Pricing Level already exists!');
        }

        // "Branch default" only applies to customer pricing. Ignore the flag for
        // factory/bad-pricing types.
        $isDefault = $request->boolean('is_default') && $request->priceType === 'CUSTOMER';

        DB::transaction(function () use ($request, $status, $isDefault) {
            $pl = pricelevels::create([
                'branch_code' => $request->branch_code,
                'pl_name' => $request->name,
                'pl_desc' => $request->Description,
                'pl_status' => $status,
                'pl_type' => $request->priceType,
                'is_default' => $isDefault,
                // Add more fields as needed
            ]);

            // Enforce a single default per branch.
            if ($isDefault) {
                pricelevels::branch($request->branch_code)
                    ->where('id', '!=', $pl->id)
                    ->update(['is_default' => 0]);
            }
        });

        activity()
            ->performedOn(new pricelevels())
            ->causedBy(auth()->user())
            ->withProperties($request->all())
            ->log('Added a new pricing level');

        return redirect('/pricing-level/')->with('success', 'Pricing Level added successfully!');

    }

    /**
     * Display the specified resource.
     */
    public function show(pricelevels $pricelevels)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(pricelevels $pricelevels)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        $request->validate([
            'e_pricelevel_id' => 'required|exists:pricelevels,id',
            'e_name' => 'required',
            'e_description' => 'required',
            'e_priceType' => 'required',
            'e_status' => 'nullable',
        ]);

        // "Branch default" only applies to customer pricing.
        $isDefault = $request->boolean('e_is_default') && $request->e_priceType === 'CUSTOMER';

        $pl = DB::transaction(function () use ($request, $isDefault) {
            $pl = pricelevels::find($request->e_pricelevel_id);
            $pl->pl_name = $request->e_name;
            $pl->pl_desc = $request->e_description;
            $pl->pl_status = ($request->e_status) ? 'Active' : 'Inactive';
            $pl->pl_type = $request->e_priceType;
            $pl->is_default = $isDefault;
            $pl->save();

            // Enforce a single default per branch.
            if ($isDefault) {
                pricelevels::branch($pl->branch_code)
                    ->where('id', '!=', $pl->id)
                    ->update(['is_default' => 0]);
            }

            return $pl;
        });

        activity()
            ->performedOn($pl)
            ->causedBy(auth()->user())
            ->withProperties($request->all())
            ->log('Updated a pricing level');

        return redirect('/pricing-level/')->with('success', 'Pricing Level updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(pricelevels $pricelevels)
    {
        //
    }
}
