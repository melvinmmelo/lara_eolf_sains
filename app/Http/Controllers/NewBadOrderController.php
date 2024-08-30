<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Equipment;
use App\Models\EquipmentStore;
use App\Models\NewBadOrder;
use App\Models\NewTempBadOrder;
use App\Models\pricelevels;
use App\Models\prices;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Http\Request;

class NewBadOrderController extends Controller
{
    //
    public function index()
    {
        $badOrders = NewBadOrder::with('customer')->branch(session("branch_code"))->where("is_active", 1)->get();

        return view('badorder', compact('badOrders'));
    }

    public function create()
    {

        if (session()->has('session_bo_id')) {
            $sessionBo = session('session_bo_id');
        } else {
            $sessionBo = 'BO_' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            // check if session_bo_id is already existing
            while (NewTempBadOrder::where('session_bo_id', $sessionBo)->exists()) {
                $sessionBo = 'BO_' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            }
            session()->put('session_bo_id', $sessionBo);
        }

        $customers = Customers::with('storeinfo')->branch(session('branch_code'))->get();

        $equipment = Equipment::has('equipmentStore')->branch(session('branch_code'))->get();

        $badPricing = pricelevels::where('pl_name', 'BAD PRICING')->first();

        $items = prices::where('pricelevel_id', $badPricing->id)
            ->join('product_types', 'prices.p_code', '=', 'product_types.code')
            ->select('prices.*', 'product_types.name as description', 'product_types.code as ptype_code')
            ->orderBy('product_types.sequence_no')
            ->get();

        $boProducts = NewTempBadOrder::where('session_bo_id', $sessionBo)->get();

        $totalAmount = NewTempBadOrder::where('session_bo_id', $sessionBo)->sum(\DB::raw('price * quantity'));

        return view('newaddbadorder', compact('customers', 'badPricing', 'items', 'sessionBo', 'boProducts', 'equipment', 'totalAmount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_bo' => 'required',
            'equipment_store_id' => 'required',
            'bo_percentage' => 'required',
            'remarks' => 'nullable',
        ]);

        $equipStore = EquipmentStore::find($request->equipment_store_id);
        if (!$equipStore) {
            return back()->withErrors(['error' => 'Equipment is not available.']);
        }

        if (!NewTempBadOrder::where('session_bo_id', $request->session_bo)->exists()) {
            return back()->withErrors(['error' => 'Please add products to bad order.']);
        }

        $newBadOrder = new NewBadOrder();
        $newBadOrder->branch_code = session('branch_code');
        $newBadOrder->customer_id = $equipStore->customer_id;
        $newBadOrder->degic_code = $equipStore->equipment->code;
        $newBadOrder->bo_percentage = $request->bo_percentage;
        $newBadOrder->remarks = $request->remarks;
        $newBadOrder->save();

        NewTempBadOrder::where('session_bo_id', $request->session_bo)->update(['new_bad_order_id' => $newBadOrder->id]);

        session()->forget('session_bo_id');

        activity()
            ->causedBy(auth()->user())
            ->performedOn($newBadOrder)
            ->log('Created new bad order');

        return redirect()->route('bo.index')->with('success', 'Bad order created successfully.');
    }

    public function storeTempProduct(Request $request)
    {
        $request->validate([
            'session_bo_id' => 'required',
            'item' => 'required',
            'price' => 'required',
            'quantity' => 'required',
        ]);

        $ptypeDetails = ProductType::where('code', $request->item)->first();

        // check if product type is existing
        $isExisting = NewTempBadOrder::where('session_bo_id', $request->session_bo_id)
            ->where('ptype_code', $request->item)
            ->exists();

        if($isExisting) {
            return back()->withErrors(['error' => 'Product already added to bad order.']);
        }

        $newTempBadOrder = new NewTempBadOrder();
        $newTempBadOrder->session_bo_id = $request->session_bo_id;
        $newTempBadOrder->ptype_code = $request->item;
        $newTempBadOrder->description = $ptypeDetails->name;
        $newTempBadOrder->quantity = $request->quantity;
        $newTempBadOrder->price = $request->price;
        $newTempBadOrder->save();

        return back()->with('success', 'Product added to bad order.');
    }

    function deleteTempProduct($id)
    {
        $res = NewTempBadOrder::find($id)->delete();

        return response()->json(['success' => $res]);

    }

    function destroy(Request $request)
    {
        $request->validate([
            'bo_id' => 'required',
        ]);

        try {
            $affectedRows = NewBadOrder::find($request->bo_id)->delete();
            if ($affectedRows > 0) {
                return redirect()->route('bo.index')->with('success', 'Bad orders deleted successfully.');
            } else {
                return back()->withErrors(['error' => 'Bad orders not found.']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete bad orders.']);
        }
    }

    function getBoDetails($boId){
        $badOrder = NewBadOrder::find($boId);
        $badOrder->load('customer');

        return response()->json(["badOrder" => $badOrder, "products" => $badOrder->products, "amount" => $badOrder->amount]);
    }

}
