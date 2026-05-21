<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Orders</title>
    <link rel="stylesheet" href="{{ asset('css/papersizes.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
        }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 6px 8px; font-size: 12px; }
        th { background: #f1f5f9; text-align: left; }
        .sig-cell { min-width: 110px; height: 28px; }
        .time-cell { min-width: 80px; height: 28px; }
    </style>
</head>

<body class="font-sans">
    <page size="letter" layout="portrait">
        <div class="p-6">
            <div class="text-center mb-4">
                <h1 class="text-lg font-bold">EOLF Food Trading OPC</h1>
                <p class="text-sm">
                    Branch: <span class="font-semibold">{{ $branchCode }}</span>
                    &nbsp;|&nbsp;
                    Date: <span class="font-semibold">{{ \Carbon\Carbon::parse($today)->format('M d, Y') }}</span>
                </p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Date</th>
                        <th>Signature</th>
                        <th>Spoons</th>
                        <th>Signature</th>
                        <th>Time</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $i => $row)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $row['customer_name'] }}</td>
                            <td>{{ $row['order_date'] ? \Carbon\Carbon::parse($row['order_date'])->format('M d, Y') : '' }}</td>
                            <td class="sig-cell"></td>
                            <td>{{ $row['spoons'] }}</td>
                            <td class="sig-cell"></td>
                            <td class="time-cell"></td>
                            <td class="time-cell"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-gray-500">No orders for the selected date.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </page>

    <div class="no-print fixed bottom-0 left-0 right-0 bg-white border-t p-3 flex justify-center gap-3">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded">
            Print
        </button>
        <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-5 rounded">
            Close
        </button>
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>

</html>
