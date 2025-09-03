<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice Totals by Customer</title>
    <link rel="stylesheet" href="{{ asset('css/papersizes.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print, .fixed-bottom {
                display: none !important;
            }
        }

        page[size="letter"] {
                height: 100% !important;
        }

        .fixed-bottom {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            padding: 10px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 100;
            display: flex;
            justify-content: center;
            align-items: center;
            border-top: 1px solid #e2e8f0;
        }

        .fixed-bottom form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .fixed-bottom .print-btn {
            margin-left: 15px;
        }

        body {
            padding-bottom: 80px; /* Add padding to prevent content from being hidden behind fixed bar */
        }
    </style>
</head>

<body class="font-sans">
    <page size="letter" layout="portrait">
        <div class="p-8">
            <!-- Removed filters from here as they will be at the bottom -->
            <div class="text-center mb-4">
                <h1 class="text-xl font-bold">EOLF Food Trading OPC Cagayan Valley</h1>
                <h2 class="text-lg font-bold">Sales Invoice Totals by Customer</h2>
                <p>For the period from {{ $date_from }} to {{ $date_to }}</p>
                <p class="mt-1"><span class="font-semibold">Status:</span> {{ $status_label }}</p>
            </div>

            <div class="flex justify-end mb-2">
                <p>{{ $date_to }}</p>
            </div>

            @php
                $paymentTypeLabels = [
                    'Cash' => 'CASH',
                    'Bank Transfer' => 'BANK TRANSFER',
                    'Cheque' => 'CHECK'
                ];
            @endphp

            <table class="w-full border-collapse text-sm">
                @foreach($payment_groups as $paymentType => $group)
                    @if($group['customers']->count() > 0)
                        <thead>
                            <tr>
                                @if($paymentType === 'Cheque')
                                    <th class="text-left py-2 px-2 font-bold">{{ strtoupper($paymentTypeLabels[$paymentType] ?? $paymentType) }}</th>
                                    <th class="text-left py-2 px-2 font-bold">Reference No.</th>
                                    <th class="text-right py-2 px-2 font-bold">AMOUNT</th>
                                    <th class="text-right py-2 px-2 font-bold">BALANCE</th>
                                @else
                                    <th class="text-left py-2 px-2 font-bold" colspan="2">{{ strtoupper($paymentTypeLabels[$paymentType] ?? $paymentType) }}</th>
                                    <th class="text-right py-2 px-2 font-bold">AMOUNT</th>
                                    <th class="text-right py-2 px-2 font-bold">BALANCE</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group['customers'] as $customer)
                            <tr>
                                @if($paymentType === 'Cheque')
                                    <td class="py-1 px-2">{{ $customer['customer_name'] }}</td>
                                    <td class="text-left py-1 px-2">{{ $customer['ref_no'] }}</td>
                                    <td class="text-right py-1 px-2">{{ number_format($customer['total_sales'], 2) }}</td>
                                    <td class="text-right py-1 px-2">{{ number_format($customer['balance'], 2) }}</td>
                                @else
                                    <td class="py-1 px-2" colspan="2">{{ $customer['customer_name'] }}</td>
                                    <td class="text-right py-1 px-2">{{ number_format($customer['total_sales'], 2) }}</td>
                                    <td class="text-right py-1 px-2">{{ number_format($customer['balance'], 2) }}</td>
                                @endif
                            </tr>
                            @endforeach
                            <tr>
                                @if($paymentType === 'Cheque')
                                    <td class="py-1 px-2 font-bold">Total</td>
                                    <td class="text-center py-1 px-2"></td>
                                    <td class="text-right py-1 px-2 font-bold">{{ number_format($group['total_sales'], 2) }}</td>
                                    <td class="text-right py-1 px-2 font-bold">{{ number_format($group['total_balance'], 2) }}</td>
                                @else
                                    <td class="py-1 px-2 font-bold" colspan="2">Total</td>
                                    <td class="text-right py-1 px-2 font-bold">{{ number_format($group['total_sales'], 2) }}</td>
                                    <td class="text-right py-1 px-2 font-bold">{{ number_format($group['total_balance'], 2) }}</td>
                                @endif
                            </tr>
                        </tbody>

                        @if(!$loop->last)
                            <tbody><tr><td colspan="4" class="py-2"></td></tr></tbody>
                        @endif
                    @endif
                @endforeach

                @if($foc_customers->count() > 0)
                    <tbody><tr><td colspan="4" class="py-2"></td></tr></tbody>
                    <thead>
                        <tr>
                            <th class="text-left py-2 px-2 font-bold" colspan="2">FREE ORDERS</th>
                            <th class="text-right py-2 px-2 font-bold">AMOUNT</th>
                            <th class="text-right py-2 px-2 font-bold">BALANCE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($foc_customers as $customer)
                        <tr>
                            <td class="py-1 px-2" colspan="2">{{ $customer['customer_name'] }}</td>
                            <td class="text-right py-1 px-2">{{ number_format($customer['total_sales'], 2) }}</td>
                            <td class="text-right py-1 px-2">{{ number_format($customer['balance'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td class="py-1 px-2 font-bold" colspan="2">Total</td>
                            <td class="text-right py-1 px-2 font-bold">{{ number_format($foc_total, 2) }}</td>
                            <td class="text-right py-1 px-2 font-bold">-</td>
                        </tr>
                    </tbody>
                @endif

                <tfoot>
                    <tr class="border-t-2 border-black">
                        <td class="py-2 px-2 font-bold" colspan="2">TOTAL PAYMENTS</td>
                        <td class="text-right py-2 px-2 font-bold">{{ number_format($total_sales, 2) }}</td>
                        <td class="text-right py-2 px-2 font-bold">{{ number_format($total_balance, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Removed print button from here as it will be at the bottom -->
    </page>
    <!-- Fixed bottom filters and print button -->
    <div class="fixed-bottom no-print">
        <form action="{{ route('report.sales-by-customer') }}" method="GET">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">From</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from', \Carbon\Carbon::parse($date_from)->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">To</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to', \Carbon\Carbon::parse($date_to)->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="status_filter" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status_filter" id="status_filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="all" {{ request('status_filter') == 'all' ? 'selected' : '' }}>All Orders (Completed)</option>
                    <option value="Completed" {{ request('status_filter') == 'Completed' ? 'selected' : '' }}>Unpaid</option>
                    <option value="Paid" {{ request('status_filter') == 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Cancelled" {{ request('status_filter') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="Deleted" {{ request('status_filter') == 'Deleted' ? 'selected' : '' }}>Deleted</option>
                    <option value="Free" {{ request('status_filter') == 'Free' ? 'selected' : '' }}>Free</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Search
                </button>
            </div>
        </form>
        <div class="print-btn">
            <button onclick="window.print()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Print Report
            </button>
        </div>
    </div>
</body>

</html>
