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

    public function create($q = null)
    {
        $plId = null;
        if ($q) {
            $plId = $q;
        }

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

        $items = ProductType::where('is_active', 1)->orderBy("sequence_no")->get();

        $pricingLevels = pricelevels::branch(session("branch_code"))->where('pl_name', 'BAD PRICING')->get();

        $boProducts = NewTempBadOrder::where('session_bo_id', $sessionBo)->get();

        $totalAmount = NewTempBadOrder::where('session_bo_id', $sessionBo)->sum(\DB::raw('price * quantity'));

        return view('newaddbadorder', compact('customers', 'pricingLevels', 'items', 'sessionBo', 'boProducts', 'equipment', 'totalAmount', 'plId'));
    }

    public function getPricing($plId, $pCode)
    {
        $price = prices::getPricePerPriceLevelAndPCode($plId, $pCode);
        return response()->json($price);
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

        return redirect()->route('newbo.index')->with('success', 'Bad order created successfully.');
    }

    public function storeTempProduct(Request $request)
    {
        $request->validate([
            'session_bo_id' => 'required',
            'priceLevel' => 'required',
            'item' => 'required',
            'price' => 'required',
            'quantity' => 'required',
        ]);

        $ptypeDetails = ProductType::where('code', $request->item)->first();

        // check if product type is existing
        $isExisting = NewTempBadOrder::where('session_bo_id', $request->session_bo_id)
            ->where('ptype_code', $request->item)
            ->exists();

        if ($isExisting) {
            return back()->withErrors(['error' => 'Product already added to bad order.']);
        }

        $newTempBadOrder = new NewTempBadOrder();
        $newTempBadOrder->session_bo_id = $request->session_bo_id;
        $newTempBadOrder->ptype_code = $request->item;
        $newTempBadOrder->description = $ptypeDetails->name;
        $newTempBadOrder->quantity = $request->quantity;
        $newTempBadOrder->price = $request->price;
        $newTempBadOrder->save();

        return redirect()->route('newbo.create', ["q" => $request->priceLevel])->with('success', 'Product added to bad order.');
    }

    public function deleteTempProduct($id)
    {
        $res = NewTempBadOrder::find($id)->delete();

        return response()->json(['success' => $res]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'bo_id' => 'required',
        ]);

        $res = NewBadOrder::find($request->bo_id)->delete();
        if ($res) {
            return redirect()->route('newbo.index')->with('success', 'Bad order deleted successfully.');
        } else {
            return back()->withErrors(['error' => 'Bad order not found.']);
        }
    }

    public function getBoDetails($boId)
    {
        $badOrder = NewBadOrder::find($boId);
        $badOrder->load('customer');

        return response()->json(["badOrder" => $badOrder, "products" => $badOrder->products, "amount" => $badOrder->amount]);
    }

    public function badOrdersDeducted()
    {
        $badOrders = NewBadOrder::with('customer')->branch(session("branch_code"))->where("is_active", 0)->get();
        return view('badorder.deducted', ['badOrders' => $badOrders]);
    }
}
