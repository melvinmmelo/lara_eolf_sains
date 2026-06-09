<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Customers as Customer;
use App\Models\pricelevels;
use App\Models\PhAddr;
use App\Models\StoreInfo;
use App\Models\Equipment;
use App\Models\Inbound;
use App\Models\DeliveryReceipt;
use App\Models\PullOutForm;
use App\Models\EquipmentHistory;

class CustomersController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['stores.equipmentStores'])->branch(session('branch_code'))->where('status', 'active')->get();
        $pricelevels = pricelevels::getCustomerPriceLevels(session('branch_code'));
        return view('customersinfo', compact('customers', 'pricelevels'));
    }

    public function stopSelling()
    {
        $customers = Customer::with(['stores.equipmentStores'])
            ->branch(session('branch_code'))
            ->whereRaw('LOWER(status) = ?', ['stop selling'])
            ->get();

        return view('stop-selling-customers', compact('customers'));
    }

    public function reactivate($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->status = 'active';
        $customer->save();

        return redirect()->route('customers.stop-selling')->with('success', 'Customer reactivated successfully!');
    }

    public function create()
    {
        return view('create-customer', compact('customers'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'branch_code' => 'required',
            'lastname' => 'required',
            'firstname' => 'required',
            'companyname' => 'required',
            'middlename' => 'nullable',
            'storename' => 'required',
            'pricelevel_id' => 'nullable|exists:pricelevels,id',
        ]);

        $regionName = PhAddr::where('code', $request->region)->value('name');
        $provinceName = PhAddr::where('code', $request->province)->value('name');
        $cityName = PhAddr::where('code', $request->city)->value('name');
        $brgyName = PhAddr::where('code', $request->brgy)->value('name');

        $regionName2 = PhAddr::where('code', $request->region2)->value('name');
        $provinceName2 = PhAddr::where('code', $request->province2)->value('name');
        $cityName2 = PhAddr::where('code', $request->city2)->value('name');
        $brgyName2 = PhAddr::where('code', $request->brgy2)->value('name');


        $customer = Customer::create([
            'branch_code' => $request->branch_code,
            'pricelevel_id' => $request->pricelevel_id ?: null,
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

        StoreInfo::create([
            'customer_id' => $customer->id,
            'storename' => $request->storename,
            'contactno' => $request->contactno2,
            'region' => $regionName2, // Insert region name instead of code
            'province' => $provinceName2, // Insert province name instead of code
            'city' => $cityName2, // Insert city name instead of code
            'brgy' => $brgyName2, // Insert barangay name instead of code
            'subdivision' => $request->subdivision2,
            'latitude' => $request->latitude2,
            'longitude' => $request->longitude2,
            'listype' => $request->listype,
            'length_stay' => $request->length_stay,
            'remarks' => $request->remarks,
        ]);

        return redirect('/customers')->with('success', 'Customer added successfully!');
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request)
    {
        $customer = Customer::findOrFail($request->id);
        $storeInfo = $customer->storeinfo;

        if (!$storeInfo) {
            $storeInfo = new StoreInfo();
            $storeInfo->customer_id = $customer->id; // Link the new StoreInfo to the customer
        }

        $request->validate([
            'lastname' => 'required',
            'firstname' => 'required',
            'companyname' => 'required',
            'status' => 'required',
            'pricelevel_id' => 'nullable|exists:pricelevels,id',
        ]);

        $customer->update([
            'pricelevel_id' => $request->pricelevel_id ?: null,
            'distributor' => $request->distributor,
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'companyname' => $request->companyname,
            'contact_no' => $request->contact_no,
            'email' => $request->email,
            'tin' => $request->tin,
            'region' => $request->e_region,
            'province' => $request->e_province,
            'city' => $request->e_city,
            'brgy' => $request->e_brgy,
            'subdivision' => $request->subdivision,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'status' => $request->status,
        ]);

        $storeInfo->storename = $request->storename;
        $storeInfo->contactno = $request->contactno2;
        $storeInfo->region = $request->e_region2;
        $storeInfo->province = $request->e_province2;
        $storeInfo->city = $request->e_city2;
        $storeInfo->brgy = $request->e_brgy2;
        $storeInfo->subdivision = $request->subdivision2;
        $storeInfo->latitude = $request->latitude2;
        $storeInfo->longitude = $request->longitude2;
        $storeInfo->listype = $request->listype;
        $storeInfo->length_stay = $request->length_stay;
        $storeInfo->remarks = $request->remarks;

        $storeInfo->save();

        // Optionally propagate the new name to the denormalized customer_name snapshots
        // that the reports read from. Admin-gated on the server too, not just in the UI.
        if ($request->boolean('update_reports') && $request->user()?->can('admin')) {
            $newName = $customer->fullName; // matches how customer_name is set at creation everywhere
            $customerId = $customer->id;

            $counts = DB::transaction(function () use ($customerId, $newName) {
                $inboundIds = Inbound::where('customer_id', $customerId)->pluck('id');

                return [
                    'inbounds' => Inbound::where('customer_id', $customerId)
                        ->update(['customer_name' => $newName]),
                    // delivery_receipts has no customer_id; reach it through the customer's inbounds
                    'delivery_receipts' => $inboundIds->isEmpty() ? 0 : DeliveryReceipt::whereIn('inbound_id', $inboundIds)
                        ->update(['customer_name' => $newName]),
                    'pull_out_forms' => PullOutForm::where('customer_id', $customerId)
                        ->update(['customer_name' => $newName]),
                    'equipment_history' => EquipmentHistory::where('customer_id', $customerId)
                        ->update(['customer_name' => $newName]),
                ];
            });

            activity()
                ->performedOn($customer)
                ->withProperties(array_merge(['new_name' => $newName], $counts))
                ->log('Customer name propagated to reports');

            $total = array_sum($counts);

            return redirect('/customers/')->with(
                'success',
                "Customer updated. Name propagated to {$total} record(s): "
                    . "{$counts['inbounds']} order(s), {$counts['delivery_receipts']} delivery receipt(s), "
                    . "{$counts['pull_out_forms']} pull-out form(s), {$counts['equipment_history']} equipment history record(s)."
            );
        }

        return redirect('/customers/')->with('success', 'Customer updated successfully!');
    }

    public function destroyStore($customerId, $storeId, Request $request)
    {
        $store = Storeinfo::findOrFail($storeId);

        foreach ($request->input('equipment_ids', []) as $equipmentId) {
            $equipment = Equipment::findOrFail($equipmentId);
            $equipment->status = 'available';
            $equipment->save();
        }

        $store->delete();

        $customer = Customer::findOrFail($customerId);
        if ($customer->stores()->count() == 0) {
            $customer->delete();
        }

        return redirect('/customers/')->with('success', 'Store and possibly customer deleted successfully, and equipment status updated to available!');
    }

}
