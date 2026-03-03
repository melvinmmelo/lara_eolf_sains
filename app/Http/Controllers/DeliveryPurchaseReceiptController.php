<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPurchaseReceipt;
use App\Http\Requests\StoreDeliveryPurchaseReceiptRequest;
use App\Http\Requests\UpdateDeliveryPurchaseReceiptRequest;
use App\Models\ItemMasterData;
use App\Models\prices;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\DPRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DeliveryPurchaseReceiptController extends Controller
{

    public function saveAndInventoryProduct(int $dprId)
    {
        $dpr = DeliveryPurchaseReceipt::findOrFail($dprId);

        $dpr->status = 'Completed';

        if ($dpr->save()) {
            $dprService = new DPRService($dpr->products);
            $dprService->saveAndInventoryProducts();
        }

        $dpr->products = $dprService->getNewProducts();
        $dpr->save();

        activity()
            ->performedOn($dpr)
            ->log("$dpr->dr_no completed by " . auth()->user()->fullName . " and inventory has been replenished.");


        return redirect()->route('delivery-purchase-receipts.index')->with('success', 'Delivery Receipt saved successfully.');
    }

    public function moveToBranch(Request $request, int $dprId)
    {
        $request->validate([
            'target_branch_code' => 'required|exists:branches,code',
            'password'           => 'required',
            'reason'             => 'required|max:500',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->back()->withErrors('Incorrect password. Operation aborted.');
        }

        $dpr = DeliveryPurchaseReceipt::findOrFail($dprId);

        if ($dpr->status !== 'Completed') {
            return redirect()->back()->withErrors('Only completed DPRs can be moved to another branch.');
        }

        $sourceBranch = $dpr->branch_code;
        $targetBranch = $request->target_branch_code;

        if ($sourceBranch === $targetBranch) {
            return redirect()->back()->withErrors('Target branch must be different from the current branch.');
        }

        $products = json_decode($dpr->products, true);

        if (!$products) {
            return redirect()->back()->withErrors('No products found in this DPR.');
        }

        DB::transaction(function () use ($dpr, $products, $sourceBranch, $targetBranch, $request) {
            foreach ($products as $product) {
                $qty     = max(0, ($product['quantity'] ?? 0) - ($product['hold'] ?? 0));
                $holdQty = $product['hold'] ?? 0;

                // Reverse stocks on source branch
                $sourceItem = ItemMasterData::branch($sourceBranch)
                    ->productCode($product['code'])
                    ->first();

                if ($sourceItem) {
                    $sourceItem->stocks        -= $qty;
                    $sourceItem->hold_quantity -= $holdQty;
                    $sourceItem->save();
                }

                // Apply stocks on target branch
                $targetItem = ItemMasterData::branch($targetBranch)
                    ->productCode($product['code'])
                    ->first();

                if (!$targetItem) {
                    $targetItem                      = new ItemMasterData();
                    $targetItem->branch_code         = $targetBranch;
                    $targetItem->product_code        = $product['code'];
                    $targetItem->product_description = $product['description'];
                    $targetItem->stocks              = $qty;
                    $targetItem->hold_quantity       = $holdQty;
                    $targetItem->unit                = $product['unit'];
                    $targetItem->save();
                } else {
                    $targetItem->stocks        += $qty;
                    $targetItem->hold_quantity += $holdQty;
                    $targetItem->save();
                }
            }

            $dpr->branch_code = $targetBranch;
            $dpr->save();

            activity()
                ->performedOn($dpr)
                ->withProperties([
                    'moved_by'    => auth()->user()->fullName,
                    'from_branch' => $sourceBranch,
                    'to_branch'   => $targetBranch,
                    'reason'      => $request->reason,
                ])
                ->log("DPR {$dpr->dr_no} moved from {$sourceBranch} to {$targetBranch} by " . auth()->user()->fullName . ". Reason: {$request->reason}");
        });

        return redirect()->route('delivery-purchase-receipts.index')
            ->with('success', "DPR {$dpr->dr_no} has been moved to {$targetBranch} successfully.");
    }

    public function storeProduct(Request $request)
    {

        $request->validate([
            'dpr_id' => 'required|exists:delivery_purchase_receipts,id',
            'product_code' => 'required',
            'qty' => 'required|numeric',
        ]);

        $dpr = DeliveryPurchaseReceipt::findOrFail($request->dpr_id);

        $productPrice = prices::getFactoryPrice($request->product_code);

        if (!$productPrice) {
            return redirect()->back()->withErrors('Price not found.');
        }

        $product = Product::productCode($request->product_code)->first();

        $productTypeData = ProductType::code($product->product_type_code)
            ->select('sequence_no', 'code')
            ->first();


        $newProduct = ['order' => $productTypeData->sequence_no, 'ptype_code' => $productTypeData->code, 'code' => $request->product_code, 'description' => $product->productName, 'quantity' => $request->qty, 'unit' => $productPrice->p_unit, 'price' => $productPrice->p_price, 'hold' => '0', 'created_at' => now(), 'updated_at' => ''];

        $dprService = new DPRService($dpr->products);

        $dprService->addProduct($newProduct);

        $dpr->products = $dprService->getNewProducts();

        $dpr->save();

        activity()
            ->performedOn($dpr)
            ->log("$request->product_code [$request->qty] added to dpr $dpr->dr_no.");

        return redirect()->back()->with('success', 'Product added to Delivery Receipt successfully.');
    }

    /**
     * Display a listing of the resource.
     */
    public function products(int $dprId)
    {
        $deliveryPurchaseReceipt = DeliveryPurchaseReceipt::findOrFail($dprId);

        $originalProducts = Product::all();
        $originalProducts = $originalProducts->sortBy(function ($product) {
            return ProductType::code($product->product_type_code)->pluck('sequence_no')->first();
        });

        $productsSumm = collect(json_decode($deliveryPurchaseReceipt->products, true))
            ->map(function ($product) {

                if (isset($product['ptype_code'])) {
                    $ptypeCode = $product['ptype_code'];
                } else {
                    $ptypeCode = substr($product['code'], 0, 2);
                    if ($ptypeCode == 'IC') {
                        $ptypeCode = substr($product['code'], 0, 3);
                    }
                }

                $productType = ProductType::where("code", "LIKE", "$ptypeCode%")->first();

                return [
                    'code' => $ptypeCode,
                    'quantity' => $product['quantity'],
                    'order' => $productType->sequence_no ?? '',
                ];
            })
            ->groupBy('code')
            ->map(function ($group) {
                return [
                    'code' => $group->first()['code'],
                    'quantity' => $group->sum('quantity'),
                    'order' => $group->first()['order'],
                ];
            })
            ->sortBy('order')
            ->values()
            ->toArray();


        return view('delivery-purchase-receipts.products', compact('deliveryPurchaseReceipt', 'originalProducts', 'productsSumm'));
    }

    public function productsEdit(int $dprId)
    {
        $deliveryPurchaseReceipt = DeliveryPurchaseReceipt::findOrFail($dprId);

        $originalProducts = Product::all();
        $originalProducts = $originalProducts->sortBy(function ($product) {
            return ProductType::code($product->product_type_code)->pluck('sequence_no')->first();
        });

        return view('delivery-purchase-receipts.products-edit', compact('deliveryPurchaseReceipt', 'originalProducts'));
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveryPurchaseReceipts = DeliveryPurchaseReceipt::branch(session('branch_code'))->get();
        $dateToday = date('Y-m-d');
        return view('delivery-purchase-receipts.index', compact('deliveryPurchaseReceipts', 'dateToday'));
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
    public function store(StoreDeliveryPurchaseReceiptRequest $request)
    {
        $dpr = DeliveryPurchaseReceipt::create($request->validated());

        activity()
            ->performedOn($dpr)
            ->log("DPR $dpr->dr_no created by " . auth()->user()->fullName);

        return redirect()->route('drp.products', ['dprId' => $dpr->id])->with('success', 'Delivery Receipt created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryPurchaseReceipt $deliveryPurchaseReceipt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeliveryPurchaseReceipt $deliveryPurchaseReceipt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * Realtime update in item master data
     */
    public function update(Request $request)
    {
        $request->validate([
            'dprId' => 'required|exists:delivery_purchase_receipts,id',
            'code' => 'required',
            'action' => 'required',
            'quantity' => 'required_if:action,add',
            'new_quantity' => 'required_if:action,edit',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // Only lock the DPR since we're modifying its contents
                $dpr = DeliveryPurchaseReceipt::lockForUpdate()->findOrFail($request->dprId);
                
                $dprService = new DPRService($dpr->products);
                
                // Don't lock ItemMasterData for read operations
                $item = ItemMasterData::branch(session('branch_code'))
                    ->productCode($request->code)
                    ->firstOrFail();
                    
                $product = $dprService->getProduct($request->code);

                switch ($request->action) {
                    case 'delete':
                        $this->handleDelete($item, $product, $dprService);
                        break;
                    case 'add':
                        $this->handleAdd($item, $product, $dprService, $request);
                        break;
                    case 'edit':
                        $this->handleEdit($item, $product, $dprService, $request);
                        break;
                    default:
                        throw new \InvalidArgumentException('Invalid action specified.');
                }

                $dpr->products = $dprService->getNewProducts();
                $dpr->save();
                
                // Only lock ItemMasterData when we're about to update it
                $itemToUpdate = ItemMasterData::branch(session('branch_code'))
                    ->productCode($request->code)
                    ->lockForUpdate()
                    ->firstOrFail();
                    
                // Re-verify our calculations are still valid
                if ($itemToUpdate->stocks !== $item->stocks) {
                    throw new \Exception('Stock quantity has changed. Please try again.');
                }
                
                $itemToUpdate->stocks = $item->stocks;
                $itemToUpdate->save();

                $this->logActivity($dpr, $request->code);

                return redirect()->back()->with('success', 'Item updated successfully.');
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error processing your request: ' . $e->getMessage());
        }
    }

    private function handleDelete(ItemMasterData $item, $product, DPRService $dprService)
    {
        $newQuantity = $item->stocks - $product['quantity'];

        if ($newQuantity < $item->reserved) {
            throw new \Exception('New quantity should not be less than to reserved.');
        }

        if ($newQuantity < 0) {
            throw new \Exception('Insufficient stock.');
        }
        $dprService->deleteProduct($product['code']);
        $item->stocks = $newQuantity;
    }

    private function handleAdd(ItemMasterData $item, $product, DPRService $dprService, Request $request)
    {
        if (!$product) {
            $this->addNewProduct($dprService, $request);
        } else {
            $dprService->addProduct($product, $request->quantity);
        }
        $item->stocks += $request->quantity;
    }

    private function addNewProduct(DPRService $dprService, Request $request)
    {
        $productPrice = prices::getFactoryPrice($request->code) ?? throw new \Exception('Price not found.');
        $product = Product::productCode($request->code)->firstOrFail();
        $sequenceNo = ProductType::code($product->product_type_code)->pluck('sequence_no')->first();

        $newProduct = [
            'order' => $sequenceNo,
            'code' => $request->code,
            'description' => $product->productName,
            'quantity' => $request->quantity,
            'unit' => $productPrice->p_unit,
            'price' => $productPrice->p_price,
            'hold' => '0',
            'created_at' => now(),
            'updated_at' => ''
        ];

        $dprService->addProduct($newProduct);
    }

    private function logActivity(DeliveryPurchaseReceipt $dpr, string $code)
    {
        activity()
            ->performedOn($dpr)
            ->log("$code updated in dpr $dpr->dr_no by " . auth()->user()->fullName);
    }

    private function handleEdit(ItemMasterData $item, $product, DPRService $dprService, Request $request)
    {
        if (!$product) {
            throw new \Exception('Product not found in DPR.');
        }


        $oldQuantity = $product['quantity'];
        $newQuantity = $request->new_quantity;

        if ($newQuantity < $item->reserved) {
            throw new \Exception('New quantity should not be less than to reserved.');
        }

        $quantityDifference = $newQuantity - $oldQuantity;
        $newStocks = $item->stocks + $quantityDifference;
        if ($newStocks < 0) {
            throw new \Exception('Insufficient stock.');
        }

        $item->stocks = $newStocks;

        $updatedProduct = array_merge($product, [
            'quantity' => $newQuantity,
            'updated_at' => now()
        ]);

        $dprService->updateProduct($updatedProduct);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $dpr = DeliveryPurchaseReceipt::findOrFail($request->drid);

        $dprService = new DPRService($dpr->products);
        // dd($dpr->products);

        $dprService->deleteProduct($request->pcode);

        $dpr->products = $dprService->getNewProducts();

        $dpr->save();

        activity()
            ->performedOn($dpr)
            ->log("$dpr->dr_no deleted by " . auth()->user()->fullName);

        return redirect()->back()->with('success', 'Item deleted successfully.');
    }

    public function destroyDPR(Request $request, int $dprId)
    {
        $request->validate([
            'password' => 'required',
            'reason'   => 'required|max:500',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->back()->withErrors('Incorrect password. Operation aborted.');
        }

        $dpr = DeliveryPurchaseReceipt::findOrFail($dprId);

        if (!in_array($dpr->status, ['Encoding', 'Completed'])) {
            return redirect()->back()->withErrors('This DPR cannot be deleted.');
        }

        if ($dpr->status === 'Completed') {
            $products = json_decode($dpr->products, true);

            DB::transaction(function () use ($dpr, $products, $request) {
                foreach ($products ?? [] as $product) {
                    $qty     = max(0, ($product['quantity'] ?? 0) - ($product['hold'] ?? 0));
                    $holdQty = $product['hold'] ?? 0;

                    $item = ItemMasterData::branch($dpr->branch_code)
                        ->productCode($product['code'])
                        ->first();

                    if ($item) {
                        $item->stocks        -= $qty;
                        $item->hold_quantity -= $holdQty;
                        $item->save();
                    }
                }

                activity()
                    ->performedOn($dpr)
                    ->withProperties([
                        'deleted_by' => auth()->user()->fullName,
                        'reason'     => $request->reason,
                    ])
                    ->log("DPR {$dpr->dr_no} deleted by " . auth()->user()->fullName . ". Stock reversed. Reason: {$request->reason}");

                $dpr->delete();
            });
        } else {
            activity()
                ->performedOn($dpr)
                ->withProperties([
                    'deleted_by' => auth()->user()->fullName,
                    'reason'     => $request->reason,
                ])
                ->log("DPR {$dpr->dr_no} deleted by " . auth()->user()->fullName . ". Reason: {$request->reason}");

            $dpr->delete();
        }

        return redirect()->route('delivery-purchase-receipts.index')->with('success', 'DPR deleted successfully.');
    }

    public function holdProduct(Request $request)
    {

        $request->validate([
            'hold_dpr_id' => 'required|exists:delivery_purchase_receipts,id',
            'hold_pcode' => 'required',
            'hold_qty' => 'required',
        ]);

        $dpr = DeliveryPurchaseReceipt::findOrFail($request->hold_dpr_id);

        $dprService = new DPRService($dpr->products);

        $products = $dprService->holdProduct($request->hold_pcode, $request->hold_qty);

        if (!$products) {
            return redirect()->back()->withErrors('There is no enough quantity to hold.');
        }

        $dpr->products = $dprService->getNewProducts();

        $dpr->save();

        activity()
            ->performedOn($dpr)
            ->log("$request->hold_pcode [qty: $request->hold_qty] hold in dpr $dpr->dr_no by " . auth()->user()->fullName);

        return redirect()->back()->with('success', 'Item hold successfully.');
    }
}
