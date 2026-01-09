<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detailed Sales Invoice Report</title>
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
            gap: 10px;
        }

        body {
            padding-bottom: 80px;
        }

        table {
            font-size: 11px;
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #E6B8E6;
            font-weight: bold;
            padding: 8px 4px;
            border: 1px solid #000;
            text-align: center;
            white-space: nowrap;
        }

        td {
            padding: 6px 4px;
            border: 1px solid #000;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .overflow-auto {
            overflow-x: auto;
        }
    </style>
</head>

<body class="font-sans">
    <page size="letter" layout="landscape">
        <div class="p-4">
            <div class="text-center mb-4">
                <h1 class="text-xl font-bold">EOLF Food Trading OPC Cagayan Valley</h1>
                <h2 class="text-lg font-bold">Detailed Sales Invoice Report</h2>
                <p>For the period from {{ $date_from_display }} to {{ $date_to_display }}</p>
                <p class="mt-1"><span class="font-semibold">Status:</span> {{ $status_label }}</p>
            </div>

            <div class="overflow-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Month</th>
                            <th>DR</th>
                            <th>SI No</th>
                            <th>TIN (000-000-000)</th>
                            <th>Customer</th>
                            <th>Address</th>
                            <th>Sales Type</th>
                            <th>Amount Collected</th>
                            <th>Amount (VAT_Inclusive)</th>
                            <th>Amount (VAT_Exclusive)</th>
                            <th>VAT</th>
                            <th>Tax Withheld</th>
                            <th>Delivery Charge</th>
                            <th>Discount</th>
                            <th>Bad Order</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $row)
                        <tr>
                            <td class="text-center">{{ $row['order_date'] }}</td>
                            <td class="text-center">{{ $row['month'] }}</td>
                            <td>{{ $row['dr'] }}</td>
                            <td>{{ $row['si_no'] }}</td>
                            <td>{{ $row['tin'] }}</td>
                            <td>{{ $row['customer'] }}</td>
                            <td>{{ $row['address'] }}</td>
                            <td class="text-center">{{ $row['sales_type'] }}</td>
                            <td class="text-right">{{ number_format($row['amount_collected'], 2) }}</td>
                            <td class="text-right">{{ number_format($row['vat_inclusive'], 2) }}</td>
                            <td class="text-right">{{ number_format($row['vat_exclusive'], 2) }}</td>
                            <td class="text-right">{{ number_format($row['vat'], 2) }}</td>
                            <td class="text-right">{{ number_format($row['tax_withheld'], 2) }}</td>
                            <td class="text-right">{{ number_format($row['delivery_charge'], 2) }}</td>
                            <td class="text-right">{{ number_format($row['discount'], 2) }}</td>
                            <td class="text-right">{{ number_format($row['bad_order'], 2) }}</td>
                            <td>{{ $row['remarks'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15" class="text-center py-4">No data available for the selected period</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </page>

    <!-- Fixed bottom controls -->
    <div class="fixed-bottom no-print">
        <a href="{{ route('report.sales-by-customer', request()->all()) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Summary
        </a>
        <button onclick="window.print()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Print Report
        </button>
        <a href="{{ route('report.sales-by-customer.export-detailed', request()->all()) }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
            Download Excel
        </a>
    </div>
</body>

</html>
