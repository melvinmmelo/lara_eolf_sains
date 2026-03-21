<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Withdrawal - {{ $withdrawal->code }}</title>
    <link rel="stylesheet" href="{{ asset('css/papersizes.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #000;
            background: #fff;
            padding-bottom: 80px;
        }

        page[size="letter"] {
            display: block;
            width: 8.5in;
            min-height: 11in;
            padding: 0.75in 0.75in 0.5in 0.75in;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header .company-name {
            font-size: 15px;
            font-weight: bold;
            color: #000;
        }

        .header .form-title {
            font-size: 14px;
            font-weight: bold;
            color: #c8a000;
        }

        /* Meta info */
        .meta {
            margin-bottom: 16px;
        }

        .meta p {
            font-size: 12px;
            margin-bottom: 4px;
        }

        .meta span {
            font-weight: bold;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        thead tr {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        thead th {
            font-size: 12px;
            font-weight: bold;
            padding: 6px 8px;
            text-align: center;
        }

        thead th:first-child {
            text-align: left;
        }

        tbody tr {
            border-bottom: 1px solid #ddd;
        }

        tbody td {
            padding: 6px 8px;
            font-size: 12px;
        }

        tbody td:first-child {
            text-align: left;
        }

        tbody td.center {
            text-align: center;
        }

        tbody td.right {
            text-align: right;
        }

        tfoot tr {
            border-top: 2px solid #000;
        }

        tfoot td {
            padding: 6px 8px;
            font-size: 12px;
            font-weight: bold;
        }

        tfoot td.center {
            text-align: center;
        }

        tfoot td.right {
            text-align: right;
        }

        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 32px;
        }

        .sig-issued {
            font-size: 12px;
            font-weight: bold;
        }

        .sig-received {
            font-size: 12px;
            font-weight: bold;
            text-align: left;
            min-width: 260px;
        }

        .sig-received .sig-line {
            border-bottom: 1px solid #000;
            height: 24px;
            margin-top: 4px;
            margin-bottom: 2px;
        }

        .sig-received .sig-label {
            font-size: 11px;
            font-weight: normal;
            text-align: center;
            color: #555;
        }

        /* Fixed bottom buttons */
        .fixed-bottom {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            padding: 12px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 100;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-gray { background: #6b7280; color: #fff; }
        .btn-gray:hover { background: #4b5563; }
        .btn-blue { background: #3b82f6; color: #fff; }
        .btn-blue:hover { background: #1d4ed8; }

        @media print {
            .no-print { display: none !important; }
            body { padding-bottom: 0; }
        }
    </style>
</head>

<body>
    <page size="letter" layout="portrait">

        <!-- Header -->
        <div class="header">
            <div class="company-name">EOLF FOOD TRADING OPC</div>
            <div class="form-title">Material Withdrawal Form</div>
        </div>

        <!-- Meta Info -->
        <div class="meta">
            <p><span>Request Date:</span> {{ $withdrawal->created_at->format('M d, Y') }}</p>
            <p><span>Withdrawal Date:</span> {{ $withdrawal->withdrawal_date ? $withdrawal->withdrawal_date->format('M d, Y') : '' }}</p>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">ITEM NAME</th>
                    <th>QUANTITY</th>
                    <th>UNIT</th>
                    <th style="text-align:right;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($withdrawal->materials as $material)
                <tr>
                    <td>{{ $material->name }}</td>
                    <td class="center">{{ $material->quantity }}</td>
                    <td class="center">{{ $material->unit }}</td>
                    <td class="right">{{ number_format($material->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL ITEMS</td>
                    <td class="center">{{ $withdrawal->materials->count() }}</td>
                    <td></td>
                    <td class="right">{{ number_format($withdrawal->materials->sum('amount'), 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-issued">ISSUED BY:</div>
            <div class="sig-received">
                RECEIVED BY:
                <div class="sig-line"></div>
                <div class="sig-label">Customer Name &amp; Signature</div>
            </div>
        </div>

    </page>

    <!-- Fixed bottom buttons -->
    <div class="fixed-bottom no-print">
        <a href="{{ route('material-withdrawals.list') }}" class="btn btn-gray">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <button onclick="window.print()" class="btn btn-blue">
            <i class="fas fa-print"></i> Print
        </button>
    </div>
</body>

</html>
