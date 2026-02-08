<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Withdrawal Summary</title>
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
                <p class="mt-2"><span class="font-semibold">Branch:</span> {{ session('branch_code') }}</p>
            </div>

            <div class="mb-6 p-4 bg-gray-50 rounded border">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm"><span class="font-semibold">Requested By:</span> {{ $requested_by }}</p>
                        <p class="text-sm mt-1"><span class="font-semibold">Issued By:</span> {{ $issued_by }}</p>
                    </div>
                    <div>
                        <p class="text-sm"><span class="font-semibold">Withdrawal Date:</span> {{ \Carbon\Carbon::parse($withdrawal_date)->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="text-left py-3 px-4 font-bold">Item Name</th>
                        <th class="text-center py-3 px-4 font-bold">Available</th>
                        <th class="text-center py-3 px-4 font-bold">Quantity</th>
                        <th class="text-center py-3 px-4 font-bold">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4">{{ $item['name'] }}</td>
                        <td class="text-center py-2 px-4">{{ $item['available'] }}</td>
                        <td class="text-center py-2 px-4 font-semibold">{{ $item['quantity'] }}</td>
                        <td class="text-center py-2 px-4">{{ $item['unit'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-black bg-gray-100">
                        <td class="py-3 px-4 font-bold" colspan="2">TOTAL ITEMS</td>
                        <td class="text-center py-3 px-4 font-bold">{{ count($items) }}</td>
                        <td class="py-3 px-4"></td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-500">
                <p class="text-sm text-blue-800">
                    <strong>Note:</strong> Please review the items above carefully before confirming this withdrawal.
                </p>
            </div>
        </div>
    </page>

    <!-- Fixed bottom buttons -->
    <div class="fixed-bottom no-print">
        <button onclick="history.back()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
            <i class="fas fa-arrow-left"></i> Back to Edit
        </button>

        <form id="confirmForm" action="{{ route('material-withdrawals.store') }}" method="POST" style="margin: 0;">
            @csrf
            <input type="hidden" name="requested_by" value="{{ $requested_by }}">
            <input type="hidden" name="issued_by" value="{{ $issued_by }}">
            <input type="hidden" name="withdrawal_date" value="{{ $withdrawal_date }}">

            @foreach($items as $id => $item)
            <input type="hidden" name="items[{{ $id }}][id]" value="{{ $id }}">
            <input type="hidden" name="items[{{ $id }}][quantity]" value="{{ $item['quantity'] }}">
            @endforeach

            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                <i class="fas fa-check"></i> Confirm & Save
            </button>
        </form>

        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#confirmForm').on('submit', function(e) {
            if (!confirm('Are you sure you want to process this withdrawal? This action cannot be undone.')) {
                e.preventDefault();
                return false;
            }

            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            return true;
        });
    </script>
</body>

</html>
