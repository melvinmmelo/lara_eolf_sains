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
            'grandTotal' => $allRecords->sum(function ($record) {
                return $record->getGrandTotalAttribute();
            }),
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
            $sheet->setCellValue('G' . $row, number_format($sale->getGrandTotalAttribute(), 2));
            $row++;
        }

        // Add total row
        $totalRow = $row;
        $sheet->setCellValue('A' . $totalRow, 'Total');
        $sheet->setCellValue('G' . $totalRow, number_format($sales->sum(function ($sale) {
            return $sale->getGrandTotalAttribute();
        }), 2));
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
        $grandTotal = $inbounds->sum(function ($inbound) {
            return $inbound->getGrandTotalAttribute();
        });


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

        $customerAddress = $equipmentStore->store->subdivision .  ' ' . $equipmentStore->store->brgy . ', ' . $equipmentStore->store->city . ', ' . $equipmentStore->store->province;

        $gatePassNo = Str::padLeft($equipmentStore->id, 5, '0');

        $data = [
            'store_id' => $equipmentStore->id,
            'customer_id' => $equipmentStore->customer_id,
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

    public function salesReportByCustomer(Request $request)
    {
        $date_from = $request->input('date_from', Carbon::now()->startOfDay());
        $date_to = $request->input('date_to', Carbon::now()->endOfDay());
        $status_filter = $request->input('status_filter', 'all');

        $sales_query = Inbound::whereBetween('order_date', [$date_from, $date_to])
            ->where('branch_code', session('branch_code'));

        // Apply status filter
        if ($status_filter === 'all') {
            // All orders (Completed and Paid)
            $sales_query->whereIn('status', ['Completed', 'Paid']);
        } elseif ($status_filter === 'Free') {
            // Free orders
            $sales_query->whereNotNull('is_foc');
        } else {
            // Filter by specific status
            $sales_query->where('status', $status_filter);

            // If status is not 'Cancelled' or 'Deleted', exclude FOC items
            if (!in_array($status_filter, ['Cancelled', 'Deleted'])) {
                $sales_query->whereNull('is_foc');
            }
        }

        // Get all inbounds that match the criteria
        $inbounds = $sales_query->get();

        // Separate FOC (Free Orders) and regular orders
        $foc_inbounds = $inbounds->where('is_foc', 1);
        $regular_inbounds = $inbounds->where('is_foc', '!=', 1);

        // Group regular orders by payment_type first, then by customer_name
        $payment_groups = $regular_inbounds->groupBy('payment_type')
            ->map(function ($paymentInbounds, $paymentType) {
                $customers = $paymentInbounds->groupBy('customer_name')
                    ->map(function ($customerInbounds) {
                        return [
                            'customer_name' => $customerInbounds->first()->degic_no . " - " . $customerInbounds->first()->customer_name,
                            'total_sales' => $customerInbounds->sum(function ($inbound) {
                                return $inbound->getGrandTotalAttribute();
                            }),
                            'balance' => $customerInbounds->sum(function ($inbound) {
                                return $inbound->totalBalance;
                            }),
                            'ref_no' => $customerInbounds->first()->ref_no,
                        ];
                    })
                    ->sortByDesc('total_sales')
                    ->values();

                return [
                    'customers' => $customers,
                    'total_sales' => $customers->sum('total_sales'),
                    'total_balance' => $customers->sum('balance'),
                ];
            });

        // Group FOC orders by customer_name
        $foc_customers = $foc_inbounds->groupBy('customer_name')
            ->map(function ($customerInbounds) {
                return [
                    'customer_name' => $customerInbounds->first()->degic_no . " - " . $customerInbounds->first()->customer_name,
                    'total_sales' => $customerInbounds->sum(function ($inbound) {
                        return $inbound->getGrandTotalAttribute();
                    }),
                    'balance' => 0, // FOC orders typically have no balance
                ];
            })
            ->sortByDesc('total_sales')
            ->values();

        $foc_total = $foc_customers->sum('total_sales');

        // Calculate grand totals
        $total_sales = $payment_groups->sum('total_sales');
        $total_balance = $payment_groups->sum('total_balance');

        // Legacy sales_data for backward compatibility
        $sales_data = $inbounds->groupBy('customer_name')
            ->map(function ($customerInbounds) {
                return [
                    'customer_name' =>  $customerInbounds->first()->degic_no . " - " . $customerInbounds->first()->customer_name,
                    'total_sales' => $customerInbounds->sum(function ($inbound) {
                        return $inbound->getGrandTotalAttribute();
                    }),
                    'balance' => $customerInbounds->sum(function ($inbound) {
                        return $inbound->totalBalance;
                    }),
                ];
            })
            ->sortByDesc('total_sales')
            ->values();


        // Format dates for the view
        $date_from = Carbon::parse($date_from)->format('m/d/Y');
        $date_to = Carbon::parse($date_to)->format('m/d/Y');

        // Get status label for display
        $status_label = 'All Orders (Completed)';
        if ($status_filter === 'Free') {
            $status_label = 'Free Orders';
        } elseif ($status_filter !== 'all') {
            $status_label = $status_filter . ' Orders';
        }

        if ($status_filter === 'Completed') {
            $status_label = 'Unpaid Orders';
        }

        return view('report.sales-report', compact('sales_data', 'payment_groups', 'foc_customers', 'foc_total', 'total_sales', 'total_balance', 'date_from', 'date_to', 'status_label'));
    }

    public function salesReportByCustomerDetailed(Request $request)
    {
        $date_from = $request->input('date_from', Carbon::now()->startOfDay());
        $date_to = $request->input('date_to', Carbon::now()->endOfDay());
        $status_filter = $request->input('status_filter', 'all');

        // Get sales query with same filters as the report
        $sales_query = Inbound::whereBetween('order_date', [$date_from, $date_to])
            ->where('branch_code', session('branch_code'))
            ->with(['customer', 'store', 'deliveryReceipt']);

        // Apply status filter (same logic as salesReportByCustomer)
        if ($status_filter === 'all') {
            $sales_query->whereIn('status', ['Completed', 'Paid']);
        } elseif ($status_filter === 'Free') {
            $sales_query->whereNotNull('is_foc');
        } else {
            $sales_query->where('status', $status_filter);
            if (!in_array($status_filter, ['Cancelled', 'Deleted'])) {
                $sales_query->whereNull('is_foc');
            }
        }

        $inbounds = $sales_query->orderBy('order_date', 'asc')->get();

        // Process data for view
        $reportData = [];
        foreach ($inbounds as $inbound) {
            // Get customer and store information
            $customer = $inbound->customer;
            $store = $inbound->store;

            // Build address
            $address = '';
            if ($store) {
                $addressParts = array_filter([
                    $store->subdivision,
                    $store->brgy,
                    $store->city,
                    $store->province
                ]);
                $address = implode(', ', $addressParts);
            }

            // Get TIN from customer
            $tin = $customer ? $customer->tin : '';

            // Get DR number from delivery receipt
            $drNumber = $inbound->deliveryReceipt ? $inbound->deliveryReceipt->code : 'N/A';

            // Calculate amounts
            $grandTotal = $inbound->getGrandTotalAttribute();
            $discount = $inbound->discount ?? 0;
            $badOrder = $inbound->bo_amount ?? 0;

            // VAT Calculations (placeholder - will be updated with proper formula)
            if ($inbound->with_invoice) {
                $vatInclusive = $grandTotal;
                $vatExclusive = $grandTotal / 1.12;
                $vat = $vatInclusive - $vatExclusive;
            } else {
                $vatInclusive = $grandTotal;
                $vatExclusive = $grandTotal;
                $vat = 0;
            }


            // Tax Withheld - placeholder
            $salesType = $inbound->with_invoice ? 'Vatable' : 'Non-Vatable';

            if($customer->id == 553 || $customer->id == 550 || $customer->id == 559) {
                $taxWithheld = $vatExclusive * 0.01; // 1% tax withheld
            } else {
                $taxWithheld = 0;
            }

            if($inbound->is_foc) {
                $amountCollected = 0;
            }else{
                $amountCollected = ($vatInclusive - $taxWithheld) ?? 0;
            }

            // Remarks column (include delivery charge label when applicable)
            $remarks = trim($inbound->remarks ?? '');
            if ($inbound->is_with_sf) {
                $deliveryChargeLabel = 'Freezer Delivery Charge';
                $remarks = $remarks ? "{$remarks} | {$deliveryChargeLabel}" : $deliveryChargeLabel;
            }

            // Month
            $month = $inbound->order_date ? $inbound->order_date->format('M') : '';

            $reportData[] = [
                'order_date' => $inbound->order_date->format('m/d/Y'),
                'month' => $month,
                'dr' => $drNumber,
                'si_no' => $inbound->sales_invoice_no,
                'tin' => $tin,
                'customer' => $inbound->customer_name,
                'address' => $address,
                'sales_type' => $salesType,
                'amount_collected' => $amountCollected,
                'vat_inclusive' => $vatInclusive,
                'vat_exclusive' => $vatExclusive,
                'vat' => $vat,
                'tax_withheld' => $taxWithheld,
                'delivery_charge' => $inbound->is_with_sf ? 1000 : 0,
                'discount' => $discount,
                'bad_order' => $badOrder,
                'remarks' => $remarks,
            ];
        }

        // Format dates for display
        $date_from_display = Carbon::parse($date_from)->format('m/d/Y');
        $date_to_display = Carbon::parse($date_to)->format('m/d/Y');

        // Get status label
        $status_label = 'All Orders (Completed)';
        if ($status_filter === 'Free') {
            $status_label = 'Free Orders';
        } elseif ($status_filter !== 'all') {
            $status_label = $status_filter . ' Orders';
        }
        if ($status_filter === 'Completed') {
            $status_label = 'Unpaid Orders';
        }

        return view('report.sales-report-detailed', compact('reportData', 'date_from_display', 'date_to_display', 'status_label'));
    }

    public function exportSalesReportByCustomerDetailed(Request $request)
    {
        $date_from = $request->input('date_from', Carbon::now()->startOfDay());
        $date_to = $request->input('date_to', Carbon::now()->endOfDay());
        $status_filter = $request->input('status_filter', 'all');

        // Get sales query with same filters as the report
        $sales_query = Inbound::whereBetween('order_date', [$date_from, $date_to])
            ->where('branch_code', session('branch_code'))
            ->with(['customer', 'store', 'deliveryReceipt']);

        // Apply status filter (same logic as salesReportByCustomer)
        if ($status_filter === 'all') {
            $sales_query->whereIn('status', ['Completed', 'Paid']);
        } elseif ($status_filter === 'Free') {
            $sales_query->whereNotNull('is_foc');
        } else {
            $sales_query->where('status', $status_filter);
            if (!in_array($status_filter, ['Cancelled', 'Deleted'])) {
                $sales_query->whereNull('is_foc');
            }
        }

        $inbounds = $sales_query->orderBy('order_date', 'asc')->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headers
        $headers = [
            'A1' => 'Date',
            'B1' => 'Month',
            'C1' => 'DR',
            'D1' => 'SI No',
            'E1' => 'TIN (000-000-000)',
            'F1' => 'Customer',
            'G1' => 'Address',
            'H1' => 'Sales Type',
            'I1' => 'Amount Collected',
            'J1' => 'Amount (VAT_Inclusive)',
            'K1' => 'Amount (VAT_Exclusive)',
            'L1' => 'VAT',
            'M1' => 'Tax Withheld',
            'N1' => 'Delivery Charge',
            'O1' => 'Discount',
            'P1' => 'Bad Order',
            'Q1' => 'Remarks'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style header row (pink/lavender background)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 10
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6B8E6'], // Pink/lavender
            ],
        ];
        $sheet->getStyle('A1:Q1')->applyFromArray($headerStyle);

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add data rows
        $row = 2;
        foreach ($inbounds as $inbound) {
            // Get customer and store information
            $customer = $inbound->customer;
            $store = $inbound->store;

            // Build address
            $address = '';
            if ($store) {
                $addressParts = array_filter([
                    $store->subdivision,
                    $store->brgy,
                    $store->city,
                    $store->province
                ]);
                $address = implode(', ', $addressParts);
            }

            // Get TIN from customer
            $tin = $customer ? $customer->tin : '';

            // Get DR number from delivery receipt
            $drNumber = $inbound->deliveryReceipt ? $inbound->deliveryReceipt->code : '';

            // Calculate amounts
            $grandTotal = $inbound->getGrandTotalAttribute();
            $discount = $inbound->discount ?? 0;
            $badOrder = $inbound->bo_amount ?? 0;

            // VAT Calculations (will be updated with proper formula later)
            if ($inbound->with_invoice) {
                $vatInclusive = $grandTotal;
                $vatExclusive = $grandTotal / 1.12;
                $vat = $vatInclusive - $vatExclusive;
            } else {
                $vatInclusive = $grandTotal;
                $vatExclusive = $grandTotal;
                $vat = 0;
            }

            // Tax Withheld - placeholder (not in database yet)
            if($customer->id == 553 || $customer->id == 550 || $customer->id == 559) {
                $taxWithheld = $vatExclusive * 0.01; // 1% tax withheld
            } else {
                $taxWithheld = 0;
            }

            if ($inbound->is_foc) {
                $amountCollected = 0;
            }else{
                $amountCollected = ($vatInclusive - $taxWithheld) ?? 0;
            }

            // Sales Type
            $salesType = $inbound->with_invoice ? 'Vatable' : 'Non-Vatable';

            // Month
            $month = $inbound->order_date ? $inbound->order_date->format('M') : '';

            // Populate row
            $sheet->setCellValue('A' . $row, $inbound->order_date->format('m/d/Y'));
            $sheet->setCellValue('B' . $row, $month);
            $sheet->setCellValue('C' . $row, $drNumber);
            $sheet->setCellValue('D' . $row, $inbound->sales_invoice_no);
            $sheet->setCellValue('E' . $row, $tin);
            $sheet->setCellValue('F' . $row, $inbound->customer_name);
            $sheet->setCellValue('G' . $row, $address);
            $sheet->setCellValue('H' . $row, $salesType);
            $sheet->setCellValue('I' . $row, $amountCollected);
            $sheet->setCellValue('J' . $row, $vatInclusive);
            $sheet->setCellValue('K' . $row, $vatExclusive);
            $sheet->setCellValue('L' . $row, $vat);
            $sheet->setCellValue('M' . $row, $taxWithheld);
            $sheet->setCellValue('N' . $row, $inbound->is_with_sf ? 1000 : 0);
            $sheet->setCellValue('O' . $row, $discount);
            $sheet->setCellValue('P' . $row, $badOrder);
            $sheet->setCellValue('Q' . $row, $inbound->remarks ?? '');

            // Apply borders to data row
            $sheet->getStyle('A' . $row . ':Q' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ],
                ],
            ]);

            // Format number columns
            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('L' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('M' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('N' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('O' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set minimum column widths for better readability
        $sheet->getColumnDimension('F')->setWidth(25); // Customer
        $sheet->getColumnDimension('G')->setWidth(35); // Address
        $sheet->getColumnDimension('Q')->setWidth(20); // Remarks

        // Create the Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'sales_report_detailed_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
