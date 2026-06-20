<?php

namespace App\Http\Controllers;

use App\Models\Customers as Customer; // Import the EquipmentStore model
use App\Models\Equipment; // Import the Equipment model
use App\Models\EquipmentHistory;
use App\Models\EquipmentStore;
use App\Models\PullOutForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class EquipmentStoreController extends Controller
{
    /**
     * Hard cap on how many freezers one "Add Equipment" submission may assign.
     * A store has at most a handful of freezers; this stops a stray "move all" in
     * the duallistbox from assigning the entire available pool to a single customer
     * (which once dumped 321 units — 169 of them already placed elsewhere — onto one
     * store). Bump it if a store legitimately needs more in a single go.
     */
    private const MAX_EQUIPMENT_PER_ASSIGNMENT = 20;

    /**
     * Store freezer gatepass data.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeFreezerGatepass(Request $request)
    {

        $request->validate([
            'equipment_store_id' => 'required|exists:equipment_store,id',
            'top_freezer_remarks' => 'nullable|string',
            'notes_free_small_cup' => 'nullable|string',
            'checker_name' => 'required|string',
            'loader_name' => 'required|string',
            'remarks' => 'nullable|string',
            'has_ice_scraper' => 'nullable|string',
            'has_lock_and_key' => 'nullable|string',
            'has_signage_bracket' => 'nullable|string',
            'has_tarpaulin_logo' => 'nullable|string',
            'has_tarpaulin_pricelist' => 'nullable|string',
        ]);

        try {
            $equipmentStore = EquipmentStore::findOrFail($request->input('equipment_store_id'));

            $equipmentStore->update([
                'top_freezer_remarks' => $request->input('top_freezer_remarks'),
                'notes_free_small_cup' => $request->input('notes_free_small_cup'),
                'checker_name' => $request->input('checker_name'),
                'loader_name' => $request->input('loader_name'),
                'remarks_gatepass' => $request->input('remarks'),
                'has_ice_scraper' => $request->has('has_ice_scraper'),
                'has_lock_and_key' => $request->has('has_lock_and_key'),
                'has_signage_bracket' => $request->has('has_signage_bracket'),
                'has_tarpaulin_logo' => $request->has('has_tarpaulin_logo'),
                'has_tarpaulin_pricelist' => $request->has('has_tarpaulin_pricelist'),
            ]);

            // Redirect back to the form with the print parameter
            return redirect()->route('report.freezerGatepassForm', [
                'equipment_store_id' => $equipmentStore->id,
                'print' => true,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error saving freezer gatepass data: '.$e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $store_id = $request->input('store_id');

        $equipments = EquipmentStore::with('storeinfo', 'equipment')
            ->where('customer_id', $customer_id)
            ->where('store_id', $store_id)
            ->get();

        // Only this branch's genuinely-free equipment. Without the branch scope this
        // listed the entire company-wide available pool (hundreds of units), which is
        // what let a single "move all" assign everything to one store.
        $availableEquipments = Equipment::where('status', 'available')
            ->branch(session('branch_code'))
            ->get();

        $selectedEquipmentIds = $equipments->pluck('equipment_id')->toArray();

        $availableEquipments = $availableEquipments->reject(function ($equipment) use ($selectedEquipmentIds) {
            return in_array($equipment->id, $selectedEquipmentIds);
        });

        return view('equipment-store', compact('equipments', 'availableEquipments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'store_id' => 'required|exists:storeinfo,id',
            'equipment_id' => 'required|array|max:'.self::MAX_EQUIPMENT_PER_ASSIGNMENT,
            'equipment_id.*' => 'required|exists:equipment,id',
        ], [
            'equipment_id.max' => 'You can assign at most '.self::MAX_EQUIPMENT_PER_ASSIGNMENT
                .' freezer(s) at once. Select only the freezer(s) physically at this store — '
                .'do not use the "move all" button.',
        ]);

        $customer_id = $validated['customer_id'];
        $store_id = $validated['store_id'];
        $equipment_ids = $validated['equipment_id'];

        $customer = Customer::findOrFail($customer_id);

        $errors = [];
        $lastEquipmentStore = null;

        // Wrap the whole assignment so a mid-loop failure cannot leave a partial batch
        // committed. The original had no transaction: when the request 500'd it still
        // persisted every row inserted before the error.
        DB::transaction(function () use (
            $equipment_ids, $customer_id, $store_id, $customer, &$errors, &$lastEquipmentStore
        ) {
            foreach ($equipment_ids as $equipment_id) {
                $equipment = Equipment::findOrFail($equipment_id);

                // A physical freezer lives at exactly one store. Skip it if it is
                // already assigned ANYWHERE (not just this store) so the same unit can
                // never be double-booked across customers. Previously the guard only
                // checked the same store, so one unit could sit under many customers.
                if (EquipmentStore::where('equipment_id', $equipment_id)->exists()) {
                    $errors[] = "Skipped: equipment already assigned elsewhere [ {$equipment->code} ]";

                    continue;
                }

                $equipmentStore = new EquipmentStore();
                $equipmentStore->customer_id = $customer_id;
                $equipmentStore->store_id = $store_id;
                $equipmentStore->equipment_id = $equipment_id;
                $equipmentStore->serial = $equipment->serial_no;
                $equipmentStore->type = $equipment->type;
                $equipmentStore->brand = $equipment->brand;
                $equipmentStore->owned = $equipment->ownership;
                $equipmentStore->pull_status = 'no';
                $equipmentStore->save();
                $lastEquipmentStore = $equipmentStore;

                // Mark the unit as placed. Previously this was set to 'available',
                // which kept assigned freezers in the assignable pool and let the same
                // unit be handed to other customers. 'added' is what a pull-out/replace
                // already uses for an assigned unit.
                $equipment->status = 'added';
                $equipment->save();

                EquipmentHistory::create([
                    'serial_no' => $equipment->serial_no,
                    'degic_no' => $equipment->code,
                    'customer_id' => $customer_id,
                    'customer_name' => $customer->fullName,
                    'date_assigned' => now(),
                    'user_name_assigned' => auth()->user()->fullName,
                    'current_user_name' => auth()->user()->fullName,
                ]);
            }

            $customer->status = 'active';
            $customer->save();
        });

        activity('equipment-store')
            ->withProperties(['customer_id' => $customer_id, 'store_id' => $store_id, 'equipment_ids' => $equipment_ids])
            ->log('equipment added to store');

        // add error message to the session
        if (count($errors) > 0) {
            return redirect()->back()->withErrors($errors);
        }

        return redirect()->route('report.freezerGatepassForm', ['store_id' => $store_id, 'equipment_store_id' => $lastEquipmentStore?->id, 'customer_id' => $customer_id])->with('success', 'Equipment added successfully.');
    }

    /**
     * Remove the specified equipment store entry from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $equipmentStore = EquipmentStore::findOrFail($id);
        $equipmentStore->delete();

        $equipmentId = $request->input('equipment_id');
        $equipment = Equipment::findOrFail($equipmentId);

        $equipment->status = 'available';
        $equipment->save();

        activity('equipment-store')
            ->withProperties(['equipment_store_id' => $id, 'equipment_id' => $equipmentId])
            ->log('equipment store entry deleted');

        return redirect()->back()->with('success', 'Equipment store entry deleted successfully.');
    }

    public function updatePullStatus(Request $request)
    {
        $activityLog = 'pullOut';

        $successMsg = 'Equipment pulled out successfully.';

        $request->validate([
            'pull_equipment_id' => 'required|exists:equipment,id',
            'replace_equipment_id.*' => 'required|exists:equipment,id',
            'remarks' => 'required|string|max:255', // Validate the remarks field
            'remarks_others' => 'required_if:remarks,Others|max:255',
            'customer_id' => 'required|exists:customers,id',
            'store_id' => 'required|exists:storeinfo,id',
        ]);

        $pullEquipmentId = $request->input('pull_equipment_id');

        $remarks = $request->input('remarks');

        $equipmentStore = EquipmentStore::with('storeinfo', 'customer')->where('equipment_id', $pullEquipmentId)->firstOrFail();

        $esName = $equipmentStore->storeinfo->storename;

        $customerName = $equipmentStore->customer->fullName;

        if ($request->remarks === 'STOP SELLING') {
            $customer = Customer::findOrFail($request->customer_id);
            $customer->status = 'STOP SELLING';
            $customer->save();
        }

        $equipment = Equipment::findOrFail($pullEquipmentId);

        $equipment->status = 'available';

        $equipment->save();

        $equipmentHistory = EquipmentHistory::where('serial_no', $equipment->serial_no)->where('customer_id', $request->customer_id)->first();

        if ($equipmentHistory) {
            $equipmentHistory->date_pulled_out = now();

            $equipmentHistory->user_name_pulled_out = auth()->user()->fullName;

            $equipmentHistory->pull_out_reason = $remarks;

            $equipmentHistory->current_user_name = auth()->user()->fullName;

            $equipmentHistory->save();
        } else {
            EquipmentHistory::create([
                'serial_no' => $equipment->serial_no,
                'degic_no' => $equipment->code,
                'customer_id' => $request->customer_id,
                'customer_name' => $customerName,
                'date_assigned' => now(),
                'user_name_assigned' => '',
                'date_pulled_out' => now(),
                'user_name_pulled_out' => auth()->user()->fullName,
                'pull_out_reason' => $remarks,
                'current_user_name' => auth()->user()->fullName,
            ]);
        }

        $replaceEquipmentIds = [];

        if ($request->has('replace_equipment_id')) {

            $activityLog = 'pullOut-replace';
            $successMsg = 'Equipment pulled out and replaced successfully.';

            $replaceEquipmentIds = $request->input('replace_equipment_id');
            $customer_id = $request->input('customer_id');
            $store_id = $request->input('store_id');

            foreach ($replaceEquipmentIds as $replaceEquipmentId) {
                $newEquipment = Equipment::findOrFail($replaceEquipmentId);

                $newEquipmentStore = new EquipmentStore();
                $newEquipmentStore->customer_id = $customer_id;
                $newEquipmentStore->store_id = $store_id;
                $newEquipmentStore->equipment_id = $replaceEquipmentId;
                $newEquipmentStore->serial = $newEquipment->serial_no;
                $newEquipmentStore->type = $newEquipment->type;
                $newEquipmentStore->brand = $newEquipment->brand;
                $newEquipmentStore->owned = $newEquipment->ownership;
                $newEquipmentStore->pull_status = 'no';
                $newEquipmentStore->save();

                $newEquipment->status = 'added';
                $newEquipment->save();

                $newEquipmentHistory = EquipmentHistory::where('serial_no', $newEquipment->serial_no)->where('customer_id', $customer_id)->first();
                if ($newEquipmentHistory) {
                    $newEquipmentHistory->date_assigned = now();
                    $newEquipmentHistory->user_name_assigned = auth()->user()->fullName;
                    $newEquipmentHistory->current_user_name = auth()->user()->fullName;
                    $newEquipmentHistory->save();
                } else {
                    EquipmentHistory::create([
                        'serial_no' => $newEquipment->serial_no,
                        'degic_no' => $newEquipment->code,
                        'customer_id' => $customer_id,
                        'customer_name' => $customerName,
                        'date_assigned' => now(),
                        'user_name_assigned' => auth()->user()->fullName,
                        'current_user_name' => auth()->user()->fullName,
                    ]);
                }
            }
        }

        // Create PullOutForm record
        $pullOutForm = new PullOutForm();
        $pullOutForm->customer_id = $request->input('customer_id');
        $pullOutForm->equipment_id = $pullEquipmentId;
        $pullOutForm->store_id = $equipmentStore->store_id;
        $pullOutForm->degic_no = $equipment->code;
        $pullOutForm->customer_name = $customerName;
        $pullOutForm->address = $equipmentStore->storeinfo->subdivision.', '.
                               $equipmentStore->storeinfo->brgy.', '.
                               $equipmentStore->storeinfo->city;
        $pullOutForm->sales_agent = auth()->user()->fullName;
        $pullOutForm->date = now();
        $pullOutForm->pullout_model_serial_no = $equipment->model.' / '.$equipment->serial_no;
        $pullOutForm->pullout_degic_no = $equipment->code;
        $pullOutForm->prepared_by = auth()->user()->fullName;
        $pullOutForm->noted_by = 'NALEN COMIA';
        $pullOutForm->pullout_by = auth()->user()->fullName;

        // Set status flags based on remarks
        $pullOutForm->defective_compressor = $remarks === 'DEFFECTIVE COMPRESSOR';
        $pullOutForm->not_cooling = $remarks === 'NOT COOLING';
        $pullOutForm->stop_selling = $remarks === 'STOP SELLING';
        $pullOutForm->system_leak = $remarks === 'SYSTEM LEAK';
        $pullOutForm->condemned = $remarks === 'CONDEMNED';
        $pullOutForm->return_to_supplier = $remarks === 'RETURN TO SUPPLIER';
        $remarks = $request->input('remarks_others') ? $request->input('remarks_others') : $remarks;
        $pullOutForm->remarks = $remarks;

        // If there are replacement equipment
        if (! empty($replaceEquipmentIds)) {
            $replacedEquipment = [];
            foreach ($replaceEquipmentIds as $replaceEquipmentId) {
                $newEquipment = Equipment::findOrFail($replaceEquipmentId);
                $replacedEquipment[] = [
                    'model_serial_no' => $newEquipment->model.' / '.$newEquipment->serial_no,
                    'degic_no' => $newEquipment->code,
                    'equipment_id' => $replaceEquipmentId,
                ];
            }

            // Set the first replacement as the main replacement for backward compatibility
            $firstReplacement = $replacedEquipment[0];
            $pullOutForm->replaced_model_serial_no = $firstReplacement['model_serial_no'];
            $pullOutForm->replaced_degic_no = $firstReplacement['degic_no'];

            // Store all replacements in JSON
            $pullOutForm->replaced_equipment_json = $replacedEquipment;
        }

        $pullOutForm->save();

        $equipmentStore->delete();

        activity('manage-equipment-store')
            ->withProperties(['customer' => $customerName, 'store' => $esName, 'equipment' => $equipment->code, 'pull_equipment_id' => $pullEquipmentId, 'replace_equipment_ids' => $replaceEquipmentIds, 'remarks' => $remarks])
            ->log($activityLog);

        $equipmentStoreId = null;
        if (isset($newEquipmentStore)) {
            $equipmentStoreId = $newEquipmentStore->id;
        }

        return redirect()->route('report.pullout-replaced-form', ['degic_no' => $equipment->code, 'customer_id' => $request->customer_id, 'equipment_store_id' => $equipmentStoreId])->with('success', $successMsg);
    }
}
