<?php

namespace App\Http\Controllers;

use App\Models\PullOutForm;
use Illuminate\Http\Request;

class PullOutFormController extends Controller
{
    /**
     * Display the pull-out form for a specific record.
     */
    public function show(Request $request, $degic_no, $customer_id)
    {
        if (!$degic_no || !$customer_id) {
            return redirect()->route('equipment-store.index')
                ->with('error', 'Missing required parameters.');
        }

        $pullOutForm = PullOutForm::with(['customer', 'equipment', 'replacementEquipment'])
            ->where('degic_no', $degic_no)
            ->where('customer_id', $customer_id)
            ->first();

        if (!$pullOutForm) {
            return redirect()->route('equipment-store.index')
                ->with('error', 'No pull-out form found for this equipment and customer.');
        }

        return view('report.pullout-replaced-form', compact('pullOutForm'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PullOutForm $pOF)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PullOutForm $pOF)
    {
        //
    }
}
