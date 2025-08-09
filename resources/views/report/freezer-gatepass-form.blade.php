<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FREEZER GATEPASS FORM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('css/papersizes.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                fontSize: {
                    'sm': '10px',
                    'md': '12px',
                    'xl': '20px',
                },
                extend: {
                    colors: {
                        clifford: '#da373d',
                    }
                }
            }
        }
    </script>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            width: 70%;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        @media print {
            .modal, .print-hide, .action-buttons {
                display: none !important;
            }
        }
    </style>
</head>

<body class="text-[12px] font-sans">
    <!-- Modal -->
    <div id="freezerModal" class="modal">
        <div class="modal-content">
            <h2 class="text-xl font-bold mb-4">Enter Freezer Gatepass Details</h2>
            <form id="freezerForm" method="POST" action="{{ route('equipment.store-freezer-gatepass') }}">
                @csrf
                <input type="hidden" name="equipment_store_id" value="{{ $equipment_store_id }}">
                <div class="grid grid-cols-2 gap-4">

                    <div class="form-group">
                        <label>Top Freezer Remarks</label>
                        <input type="text" name="top_freezer_remarks" class="form-control" value="{{ $top_freezer_remarks }}">
                    </div>

                    <div class="form-group">
                        <label>Free Small Cup Note</label>
                        <input type="text" name="notes_free_small_cup" class="form-control" value="{{ $free_small_cup_note }}">
                    </div>
                    <div class="form-group">
                        <label>Checker Name</label>
                        <input type="text" name="checker_name" class="form-control" required value="{{ $checker_name }}">
                    </div>
                    <div class="form-group">
                        <label>Loader Name</label>
                        <input type="text" name="loader_name" class="form-control" required value="{{ $loader_name }}">
                    </div>
                    <div class="form-group col-span-2">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control">{{ $remarks }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="form-group">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="has_ice_scraper" class="form-checkbox" {{ $has_ice_scraper ? 'checked' : '' }}>
                            <span class="ml-2">Ice Scraper</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="has_lock_and_key" class="form-checkbox" {{ $has_lock_and_key ? 'checked' : '' }}>
                            <span class="ml-2">Lock and Key</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="has_signage_bracket" class="form-checkbox" {{ $has_signage_bracket ? 'checked' : '' }}>
                            <span class="ml-2">Signage w/ Bracket</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="has_tarpaulin_logo" class="form-checkbox" {{ $has_tarpaulin_logo ? 'checked' : '' }}>
                            <span class="ml-2">Tarpaulin (logo)</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="has_tarpaulin_pricelist" class="form-checkbox" {{ $has_tarpaulin_pricelist ? 'checked' : '' }}>
                            <span class="ml-2">Tarpaulin (Price list)</span>
                        </label>
                    </div>
                </div>

                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save & Print</button>
                </div>
            </form>
        </div>
    </div>

    

    <page class="text-md" size="letter" layout="portrait">
        <div>
            <div class="text-center mb-2">
                <div class="font-bold">EOLF FOOD TRADING OPC </div>
                <div>ASTRODAR GASOLINE STATION SAN MIGUEL SAN MANUEL TARLAC / CARRESTO GASOLINE STATION SAN 
                    PEDRO MALLIG ISABELA </div>
                <div>TEL. NOS.: 09176208582 / 09171661609 </div>
                <div>Email: <span>danerics.eolffoodtrading@gmail.com 
                </span></div>
            </div>
    
            <table class="table-fixed w-full border border-black border-collapse">
                <tr>
                    
                    <td colspan="4" class="font-bold text-center text-xl border border-black">FREEZER GATEPASS </td>
                    <td colspan="2" class="font-bold text-center border border-black"> NO. {{ $gatepass_no ?? 'N/A' }} </td>
                </tr>

               <tr>
                <td class="font-bold border border-black text-center"> DATE </td>
                <td colspan="3" class="font-bold border border-black text-center "> ITEMS DESCRIPTION </td>
                <td colspan="2" class="font-bold border border-black"> </td>
               </tr>

               <tr>
                <td class="font-bold border border-black text-center align-top">{{ $date ?? 'N/A' }}</td>
                <td colspan="3" class="font-bold border border-black pl-3">
<div class="font-bold">{{ $distributor_name ?? 'N/A' }}</div>
@if($top_freezer_remarks)
<div>{{ $top_freezer_remarks ?? '' }}</div>
@endif
<div class="font-bold">{{ $customer_name ?? 'N/A' }}</div>
<div>{{ $customer_address ?? 'N/A' }}</div>
<div class="mb-5"></div>

<div class="font-bold">MODEL:  {{ $model ?? 'N/A' }}</div>
    <div class="font-bold">SERIAL NO: {{ $serial_no ?? 'N/A' }}</div>
        <div class="font-bold">DEGIC NO: {{ $degic_no ?? 'N/A' }}</div>
            <div class="font-bold">{{ $free_small_cup_note ?? 'Free Small Cup' }}</div>
                <div class="font-bold">Checker: {{ $checker_name ?? '' }}</div>
                    <div class="font-bold">Loader: {{ $loader_name ?? '' }}</div>
                        <div class="font-bold mb-4">Remarks: {{ $remarks ?? '' }}</div>
                </td>
                <td colspan="2" class="font-bold border border-black align-top pl-3""> 
                    <div><input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600" {{ ($has_ice_scraper ?? false) ? 'checked' : '' }}>
                            <span class="font-bold">Ice Scraper</span></div>
                    <div><input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600" {{ ($has_lock_and_key ?? false) ? 'checked' : '' }}>
                                <span class="font-bold">Lock and Key (Half Charge)</span></div>
                                <div><input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600" {{ ($has_signage_bracket ?? false) ? 'checked' : '' }}>
                                    <span class="font-bold">Signage w/ Bracket</span></div>
                                    <div><input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600" {{ ($has_tarpaulin_logo ?? false) ? 'checked' : '' }}>
                                        <span class="font-bold">Tarpaulin (logo)</span></div>
                                        <div><input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600" {{ ($has_tarpaulin_pricelist ?? false) ? 'checked' : '' }}>
                                            <span class="font-bold">Tarpaulin (Price list)</span></div>
                            

                </td>
               
               </tr>
            </table>

            <table class="table-fixed w-full border-separate border-spacing-y-5 border-spacing-x-5 pt-2">
                <tr>
                    <td class="border-b border-black pt-5 text-center"></td>
                    <td class="border-b border-black pt-5 text-center">NALEN COMIA</td>
                    <td class="border-b border-black text-center">{{ $issued_by ?? '' }}</td>
                    <td class="border-b border-black text-center">{{ $received_by ?? '' }}</td>
                </tr>

                <tr>
                    <td>Prepared By:</td>
                    <td>Approved By:</td>
                    <td>Issued By:</td>
                    <td>Customer Signature Over Printed Name:</td>
                </tr>
            </table>
    
            <div class="flex justify-end space-x-1 mt-4 action-buttons">
                    <!-- Cancel Button -->
                    <a href="{{ route('equipment-store.index', ['store_id' => $customer_id, 'customer_id' => $customer_id]) }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow transition duration-200 mr-2">
                        Cancel
                    </a>

                    <!-- Add details Button -->
                    <button 
                        onclick="showModal()" 
                        class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded shadow transition duration-200"
                    >
                        Add details
                    </button>

                    <!-- Print Button -->
                    <button 
                        onclick="window.print()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200"
                    >
                        Print
                    </button>
            </div>
        </div>
      
    </page>

    <script>
        // When the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we should print after saving
            if (window.location.href.includes('?print=1')) {
                window.print();
            }
        });

        function showModal() {
            document.getElementById('freezerModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('freezerModal').style.display = 'none';
        }

        // Add event listener to the form
        document.getElementById('freezerForm').addEventListener('submit', function(e) {
            // Add print parameter to form action
            this.action = this.action + '?print=1';
        });
    </script>
</body>
</html>
