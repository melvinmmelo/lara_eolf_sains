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
use Illuminate\Support\Str;
use App\Models\StoreInfo as Store;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportGeneratorController extends Controller
{
    private function getSalesQuery(Request $request)
    {
        $query = Inbound::query();
        $branchCode = session('branch_code');
        
        if ($branchCode) {
            $query->where('branch_code', $branchCode);
        }

        $reportType = $request->get('report_type', 'daily');
        $now = Carbon::now();

        // Filter by date based on order_date
        switch ($reportType) {
            case 'daily':
                $query->whereDate('order_date', $now->format('Y-m-d'));
                break;

            case 'weekly':
                $query->whereBetween('order_date', [
                    $now->startOfWeek()->format('Y-m-d'),
                    $now->endOfWeek()->format('Y-m-d')
                ]);
                break;

            case 'monthly':
                $query->whereYear('order_date', $now->year)
                      ->whereMonth('order_date', $now->month);
                break;

            case 'yearly':
                $query->whereYear('order_date', $now->year);
                break;

            case 'custom':
                if ($request->filled(['start_date', 'end_date'])) {
                    $query->whereBetween('order_date', [
                        $request->start_date,
                        Carbon::parse($request->end_date)->endOfDay()
                    ]);
                }
                break;
        }

        return $query->with(['customer', 'store'])
                     ->where('is_foc', NULL)
                     ->whereNotIn('status', ['Cancelled', 'Deleted'])
                     ->orderBy('order_date', 'desc');
    }

    public function salesReport(Request $request)
    {
        $query = $this->getSalesQuery($request);
        
        // Get all records for totals
        $allRecords = $query->get();
        
        // Calculate totals
        $totals = [
            'count' => $allRecords->count(),
            'grandTotal' => $allRecords->sum('grandTotal'),
            'completedCount' => $allRecords->whereIn('status', ['Completed', 'Paid'])->count(),
            'pendingCount' => $allRecords->whereNotIn('status', ['Completed', 'Delivered'])->count()
        ];
        
        // Get paginated results
        $sales = $query->paginate(15);
        
        return view('report.sales', compact('sales', 'totals'));
    }

    public function exportSalesReport(Request $request)
    {
        $sales = $this->getSalesQuery($request)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Date');
        $sheet->setCellValue('B1', 'Order No');
        $sheet->setCellValue('C1', 'DEGIC No');
        $sheet->setCellValue('D1', 'Customer');
        $sheet->setCellValue('E1', 'Store');
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Amount');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0'],
            ],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // Add data
        $row = 2;
        foreach ($sales as $sale) {
            $sheet->setCellValue('A' . $row, $sale->order_date->format('Y-m-d'));
            $sheet->setCellValue('B' . $row, $sale->order_no);
            $sheet->setCellValue('C' . $row, $sale->degic_no ?: 'N/A');
            $sheet->setCellValue('D' . $row, $sale->customer_name);
            $sheet->setCellValue('E' . $row, $sale->store_name);
            $sheet->setCellValue('F' . $row, $sale->status);
            $sheet->setCellValue('G' . $row, number_format($sale->grandTotal, 2));
            $row++;
        }

        // Add total row
        $totalRow = $row;
        $sheet->setCellValue('A' . $totalRow, 'Total');
        $sheet->setCellValue('G' . $totalRow, number_format($sales->sum('grandTotal'), 2));
        $sheet->mergeCells('A' . $totalRow . ':F' . $totalRow);
        
        // Style total row
        $sheet->getStyle('A' . $totalRow . ':G' . $totalRow)->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE],
            ],
        ]);

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create the Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'sales_report_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }


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
                                'customer',
                                'store'      // For distributor name/area (StoreInfo linked via store_id on equipment_store table)
                            ])->findOrFail($equipment_store_id);

        // // Validate that the fetched equipment store record actually belongs to the specified customer.
        // if ($equipmentStore->customer_id != $store->customer_id) {
        //     abort(404, 'Equipment record not found for this customer or does not match.');
        // }

        if(session('branch_code') == 'EFTO-CAG') {
            $distributor_name = 'JOFREN D. COMIA - CAGAYAN';
        } else {
            $distributor_name = 'JOFREN D. COMIA - TARLAC';
        }

        $customerAddress = $equipmentStore->store->region . ', ' . $equipmentStore->store->province . ', ' . $equipmentStore->store->city . ', ' . $equipmentStore->store->brgy . ', ' . $equipmentStore->store->brgy . ' ' . $equipmentStore->store->subdivision;

        $gatePassNo = Str::padLeft($equipmentStore->id, 5, '0');

        $data = [
            'gatepass_no' => $gatePassNo, 
            'date' => Carbon::now()->format('m/d/Y'), 
            'customer_name' => Str::upper($equipmentStore->customer->fullName ?? ($equipmentStore->customer->fullName ?? 'N/A')),
            'customer_address' => Str::upper($customerAddress), 
            'distributor_name' => $distributor_name,
     
            'model' => $equipmentStore->equipment->model_name ?? ($equipmentStore->equipment->model ?? ($equipmentStore->equipment->name ?? 'N/A')), 
            'serial_no' => $equipmentStore->equipment->serial_no ?? ($equipmentStore->serial_number ?? 'N/A'), 
            'degic_no' => $equipmentStore->equipment->code ?? 'N/A', 

            'top_freezer_remarks' => $equipmentStore->top_freezer_remarks ?? '', 
            'free_small_cup_note' => $equipmentStore->notes_free_small_cup ?? 'Free Small Cup', 
            'checker_name' => $equipmentStore->checker_name ?? (auth()->check() ? auth()->user()->name : ''),
            'loader_name' => $equipmentStore->loader_name ?? '', 
            'remarks' => $equipmentStore->remarks_gatepass ?? ($equipmentStore->remarks ?? ''), 

            'has_ice_scraper' => (bool)($equipmentStore->has_ice_scraper ?? false),
            'has_lock_and_key' => (bool)($equipmentStore->has_lock_and_key ?? false),
            'has_signage_bracket' => (bool)($equipmentStore->has_signage_bracket ?? false),
            'has_tarpaulin_logo' => (bool)($equipmentStore->has_tarpaulin_logo ?? false),
            'has_tarpaulin_pricelist' => (bool)($equipmentStore->has_tarpaulin_pricelist ?? false),

            'issued_by' => auth()->check() ? auth()->user()->name : '', 
            'received_by' => '', 
            'equipment_store_id' => $equipment_store_id,
        ];

        return view('report.freezer-gatepass-form', $data);
    }
}



