<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Withdrawal - {{ $withdrawal->code }}</title>
    <link rel="stylesheet" href="{{ asset('css/papersizes.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }

        page[size="letter"] {
            height: 100% !important;
        }

        body {
            padding-bottom: 100px;
        }

        .fixed-bottom {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            padding: 15px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 100;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>

<body class="font-sans">
    <page size="letter" layout="portrait">
        <div class="p-8">
            <div class="text-center mb-6">
                <h1 class="text-xl font-bold">EOLF Food Trading OPC Cagayan Valley</h1>
                <h2 class="text-lg font-bold">Material Withdrawal Summary</h2>
                <p class="mt-1 text-sm text-gray-500">Code: {{ $withdrawal->code }}</p>
            </div>

            <div class="mb-6 p-4 bg-gray-50 rounded border">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm"><span class="font-semibold">Requested By:</span> {{ $withdrawal->requested_by }}</p>
                        <p class="text-sm mt-1"><span class="font-semibold">Issued By:</span> {{ $withdrawal->issued_by }}</p>
                    </div>
                    <div>
                        <p class="text-sm"><span class="font-semibold">Withdrawal Date:</span> {{ $withdrawal->withdrawal_date ? $withdrawal->withdrawal_date->format('M d, Y') : 'N/A' }}</p>
                        <p class="text-sm mt-1"><span class="font-semibold">Created:</span> {{ $withdrawal->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="text-center py-3 px-4 font-bold">#</th>
                        <th class="text-left py-3 px-4 font-bold">Item Name</th>
                        <th class="text-center py-3 px-4 font-bold">Quantity</th>
                        <th class="text-center py-3 px-4 font-bold">Unit</th>
                        <th class="text-center py-3 px-4 font-bold">Amount</th>
                        <th class="text-left py-3 px-4 font-bold">Location</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($withdrawal->materials as $index => $material)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="text-center py-2 px-4">{{ $index + 1 }}</td>
                        <td class="py-2 px-4">{{ $material->name }}</td>
                        <td class="text-center py-2 px-4 font-semibold">{{ $material->quantity }}</td>
                        <td class="text-center py-2 px-4">{{ $material->unit }}</td>
                        <td class="text-center py-2 px-4">₱{{ number_format($material->amount, 2) }}</td>
                        <td class="py-2 px-4">{{ $material->location ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-black bg-gray-100">
                        <td class="py-3 px-4 font-bold" colspan="2">TOTAL ITEMS</td>
                        <td class="text-center py-3 px-4 font-bold">{{ $withdrawal->materials->count() }}</td>
                        <td class="py-3 px-4"></td>
                        <td class="text-center py-3 px-4 font-bold">₱{{ number_format($withdrawal->materials->sum('amount'), 2) }}</td>
                        <td class="py-3 px-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </page>

    <!-- Fixed bottom buttons -->
    <div class="fixed-bottom no-print">
        <a href="{{ route('material-withdrawals.list') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
            <i class="fas fa-print"></i> Print
        </button>
    </div>
</body>

</html>
