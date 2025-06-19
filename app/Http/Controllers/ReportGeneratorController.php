<?php

namespace App\Http\Controllers;

use App\Models\Inbound;
use App\Models\ItemMasterData;
use App\Models\OrderSlip;
use App\Models\ProductVariant;
use App\Models\DeliveryPurchaseReceipt;
use App\Services\InboundService;
use Illuminate\Http\Request;
use App\Models\Customers as Customer;
use App\Models\EquipmentStore;
use Carbon\Carbon;
use App\Models\StoreInfo as Store;

class ReportGeneratorController extends Controller
{

    public function productsSummaryv2()
    {
        $branchCode = session('branch_code');

        $products = InboundService::getTotalOfAllInboundProductsv2($branchCode); // ! for all outbound naman ito

        return view('report.productsSummaryv2', compact('products'));
    }


    public function productsSummary(Request $request)
    {
        $branchCode = session('branch_code');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $products = InboundService::getTotalOfAllInboundProducts($branchCode, $request->from_date, $request->to_date); // ! you can add 2nd parameter for date sample "2024-08-08"
            $title = "From: " . $request->from_date . " To: " . $request->to_date;
        } else {
            $products = InboundService::getTotalOfAllInboundProducts($branchCode); // ! you can add 2nd parameter for date sample "2024-08-08"
            $title = "Today";
        }


        return view('report.productsSummary', compact('products', 'title'));
    }
    public function orderSlip($code)
    {
        $orderSlip = OrderSlip::where('code', $code)->first();
        $inbounds = Inbound::whereNotIn('status', ['Cancelled', 'Rejected', 'Deleted'])->where('order_slip_code', $code)->orderBy('order_slip_sno')->get();
        $totalInbounds = $inbounds->count();

        $pagesData = $this->distributeInboundsToPages($inbounds);
        $totalPages = count($pagesData);
        $grandTotal = $inbounds->sum('grandTotal');


        return view('report.orderSlip', compact('inbounds', 'code', 'orderSlip', 'grandTotal', 'totalInbounds', 'totalPages', 'pagesData'));
    }

    private function distributeInboundsToPages($inbounds)
    {
        $pagesData = [];
        $currentPage = 1;
        $currentPageRows = 0;

        foreach ($inbounds as $inbound) {
            $productsCount = count(json_decode($inbound->products, true));
            $rowsNeeded = $this->calculateRowsNeeded($productsCount);

            if ($currentPageRows + $rowsNeeded > 9) {
                $currentPage++;
                $currentPageRows = 0;
            }

            $pagesData[$currentPage][] = $inbound;
            $currentPageRows += $rowsNeeded;
        }

        return $pagesData;
    }

    private function calculateRowsNeeded($productsCount)
    {
        if ($productsCount <= 23) {
            return 1;
        } elseif ($productsCount <= 45) {
            return 2;
        } else {
            return 3;
        }
    }


    public function availableStocks()
    {
        // First, get all unique variant codes
        $variantCodes = ItemMasterData::branch(session('branch_code'))
            ->get()
            ->pluck('product_code')
            ->map(function ($code) {
                $parts = explode('_', $code);
                return end($parts); // Get the last part after underscore
            })
            ->unique();

        // Preload all needed variants
        $variants = ProductVariant::whereIn('code', $variantCodes)
            ->get()
            ->keyBy('code');

        $products = ItemMasterData::branch(session('branch_code'))
            ->with(['product.productType'])
            ->select('id', 'product_code', 'product_description', 'reserved', 'stocks')
            ->get()
            ->map(function ($product) use ($variants) {
                $parts = explode('_', $product->product_code);
                $variantCode = end($parts); // Get the last part after underscore
    
                $product->available_stocks = $product->stocks - $product->reserved;
                $product->variant_name = $variants->get($variantCode)?->name ?? 'N/A';
                return $product;
            })
            ->groupBy(function ($product) {
                return $product->product->productType->name;
            })
            ->sortBy(function ($products, $key) {
                return $products->first()->product->productType->sequence_no;
            });

        return view('available-stocks', compact('products'));
    }

    public function deliveryPurchaseReceiptSummary(Request $request)
    {
        $branchCode = session('branch_code');

        $query = DeliveryPurchaseReceipt::branch($branchCode)
            ->where('status', 'Completed');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('issue_date', [$request->from_date, $request->to_date]);
            $title = "From: " . $request->from_date . " To: " . $request->to_date;
        } else {
            $query->whereDate('issue_date', now()->toDateString());
            $title = "Today";
        }

        $receipts = $query->get();

        // Process all products from the receipts
        $productSummary = [];

        foreach ($receipts as $receipt) {
            $products = json_decode($receipt->products, true);

            if (!is_array($products)) {
                continue;
            }

            foreach ($products as $product) {
                $code = $product['code'];
                $description = $product['description'] ?? 'Unknown';
                $quantity = $product['quantity'] ?? 0;
                $unit = $product['unit'] ?? 'pcs';
                $price = $product['price'] ?? 0;
                $hold = $product['hold'] ?? 0;

                if (!isset($productSummary[$code])) {
                    $productSummary[$code] = [
                        'code' => $code,
                        'description' => $description,
                        'total_quantity' => 0,
                        'total_hold' => 0,
                        'available_quantity' => 0,
                        'unit' => $unit,
                        'price' => $price,
                        'total_value' => 0
                    ];
                }

                $productSummary[$code]['total_quantity'] += $quantity;
                $productSummary[$code]['total_hold'] += $hold;
                $productSummary[$code]['available_quantity'] += ($quantity - $hold);
                $productSummary[$code]['total_value'] += ($quantity * $price);
            }
        }

        // Sort by product code
        ksort($productSummary);

        return view('report.delivery-purchase-receipt-summary', [
            'products' => $productSummary,
            'title' => $title,
            'receipts_count' => $receipts->count()
        ]);
    }

    public function customerUpdateForm(Customer $customer)
    {
        $date = now()->toDateString();
        // formate date to MM DD, YYYY
        $date = Carbon::parse($date)->format('F d, Y');
        return view('report.customer-update-form', compact('customer', 'date'));
    }

    public function pulloutReplacedForm(EquipmentStore $equipmentStore)
    {
        $customer = $equipmentStore->customer;
        $date = now()->toDateString();
        // formate date to MM DD, YYYY
        $date = Carbon::parse($date)->format('F d, Y');
        return view('report.pullout-replaced-form', compact('customer','equipmentStore', 'date'));
    }

    public function freezerGatepassForm($equipment_store_id) // Renamed $store_id for clarity
    {
        // Fetch the specific equipment store (freezer) record, along with its related equipment and store (StoreInfo) details.
        $equipmentStore = EquipmentStore::with([
                                'equipment', // For model, serial, price etc.
                                'store'      // For distributor name/area (StoreInfo linked via store_id on equipment_store table)
                            ])->findOrFail($equipment_store_id);

        // // Validate that the fetched equipment store record actually belongs to the specified customer.
        // if ($equipmentStore->customer_id != $store->customer_id) {
        //     abort(404, 'Equipment record not found for this customer or does not match.');
        // }

        // Prepare data array for the Blade view, matching the variables expected by freezer-gatepass-form.blade.php
        $data = [
            // Gatepass specific data
            'gatepass_no' => time() . $equipmentStore->id, // Use existing gatepass_number or generate one
            'date' => Carbon::now()->format('m/d/Y'), // Current date formatted

            // Customer details from the $customer model
            'customer_name' => $customer->name ?? ($customer->customer_name ?? 'N/A'), // Adjust field name as per your Customers model
            'customer_address' => $customer->address ?? ($customer->full_address ?? ($equipmentStore->store->brgy ?? 'N/A')), // Adjust field name or source

            // Distributor details (from EquipmentStore's related StoreInfo model, accessed via $equipmentStore->store)
            'distributor_name' => $equipmentStore->store->storename ?? 'N/A', // 'storename' from StoreInfo table
            'distributor_area' => trim(
                ($equipmentStore->store->brgy ?? '') .
                ($equipmentStore->store->brgy && ($equipmentStore->store->city || $equipmentStore->store->province) ? ', ' : '') .
                ($equipmentStore->store->city ?? '') .
                ($equipmentStore->store->city && $equipmentStore->store->province ? ', ' : '') .
                ($equipmentStore->store->province ?? '')
            , ', '), // Concatenate address parts from StoreInfo, ensuring clean formatting

            // Equipment details (from EquipmentStore's related Equipment model, accessed via $equipmentStore->equipment)
            'model' => $equipmentStore->equipment->model_name ?? ($equipmentStore->equipment->model ?? ($equipmentStore->equipment->name ?? 'N/A')), // Adjust field name
            'serial_no' => $equipmentStore->equipment->serial_no ?? ($equipmentStore->serial_number ?? 'N/A'), // Adjust field name
            'degic_no' => $equipmentStore->equipment->code ?? 'N/A', // Assuming 'degic_no' is a field directly on EquipmentStore

            // Other details, potentially from EquipmentStore
            'free_small_cup_note' => $equipmentStore->notes_free_small_cup ?? 'Free Small Cup', // Example field
            'checker_name' => $equipmentStore->checker_name ?? (auth()->check() ? auth()->user()->name : ''), // Default to current user or placeholder
            'loader_name' => $equipmentStore->loader_name ?? '', // Example field
            'remarks' => $equipmentStore->remarks_gatepass ?? ($equipmentStore->remarks ?? ''), // Example field

            // Boolean flags for checkboxes in the form
            'has_ice_scraper' => (bool)($equipmentStore->has_ice_scraper ?? false),
            'has_lock_and_key' => (bool)($equipmentStore->has_lock_and_key ?? false),
            'has_signage_bracket' => (bool)($equipmentStore->has_signage_bracket ?? false),
            'has_tarpaulin_logo' => (bool)($equipmentStore->has_tarpaulin_logo ?? false),
            'has_tarpaulin_pricelist' => (bool)($equipmentStore->has_tarpaulin_pricelist ?? false),

            // Signatories
            'issued_by' => auth()->check() ? auth()->user()->name : '', // Current authenticated user issues
            'received_by' => '', // Typically left blank to be filled upon receipt

            // Equipment Store ID for form submission
            'equipment_store_id' => $equipment_store_id,
        ];

        // Pass the prepared data to the Blade view
        return view('report.freezer-gatepass-form', $data);
    }
}



