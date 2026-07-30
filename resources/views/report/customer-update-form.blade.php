<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CUSTOMER UPDATE FORM</title>

    <link rel="stylesheet" href="{{ asset('css/papersizes.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                fontSize: {
                    'sm': '10px',
                    'md': '12px',
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
            z-index: 1000;
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
        }
        @media print {
            .modal, .print-hide, .action-buttons {
                display: none !important;
            }
        }

        #technicianRemarks {
            font-size: 24px !important;
        }
    </style>
</head>

<body class="text-[12px] font-sans">
    <page class="text-md" size="letter" layout="portrait">
        <div>
            <div class="text-center mb-2">
                <div class="font-bold">EOLF FOOD TRADING OPC </div>
                <div>ASTRODAR GASOLINE STATION SAN MIGUEL SAN MANUEL TARLAC / CARRESTO GASOLINE STATION SAN 
                    PEDRO MALLIG ISABELA </div>
                <div>TEL. NOS.: 09176208582 / 09171661609 </div>
                <div>Email: <span>danerics.eolffoodtrading@gmail.com 
                </span></div>
                <div class="font-bold mt-2">CUSTOMER INFORMATION UPDATE</div>
            </div>
    
            <table class="table-fixed w-full border-separate border-spacing-y-2 border-spacing-x-1">
                <tr>
                    <td>Distributor:</td>
                    <td colspan="2" class="border-b border-black">

                        @if (session('branch_code') == 'EFTO-CAG')
                            JOFREN DALANGIN COMIA - CAGAYAN
                        @else
                            JOFREN DALANGIN COMIA - TARLAC
                        @endif
                    </td>
                    <td class="text-right pr-3">Date:</td>
                    <td class="border-b border-black">{{ $date }}</td>
                </tr>
    
                <tr>
                    <td>Name of Customer/Tel. No:</td>
                    <td colspan="3" class="border-b border-black">{{ $customer->lastname }}, {{ $customer->firstname }} {{ $customer->middlename }}</td>
                    <td></td>
                </tr>
    
                <tr>
                    <td>Current Address/Location:</td>
                    <td colspan="4" class="border-b border-black">{{ $customer->storeinfo->region ?? '' }}, {{ $customer->storeinfo->province ?? '' }}, {{ $customer->storeinfo->city ?? '' }} {{ $customer->storeinfo->brgy ?? '' }}</td>
                </tr>
    
                <tr>
                    <td>Previous Add. (if applicable):</td>
                    <td colspan="4" class="border-b border-black">&nbsp;</td>
                </tr>
    
                <tr>
                    <td>RWS No.:</td>
                    <td class="border-b border-black w-[100px]">&nbsp;</td>
                    <td class="text-right pr-3">Freezer Model:</td>
                    <td colspan="2" class="border-b border-black">
                        @if ($customer->equipmentStores->isNotEmpty())
                            @foreach ($customer->equipmentStores as $equipmentStore)
                                {{ $equipmentStore->equipment->model }}
                                @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        @else
                            No Equipment Assigned
                        @endif
                    </td>
                </tr>
    
                <tr>
                    <td>DEGIC Serial No.:</td>
                    <td class="border-b border-black">
                        @if ($customer->equipmentStores->isNotEmpty())
                            @foreach ($customer->equipmentStores as $equipmentStore)
                                {{ $equipmentStore->equipment->code }}
                                @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        @else
                            No Equipment Assigned
                        @endif
                    </td>
                    <td class="text-right pr-3">Freezer Serial No.:</td>
                    <td colspan="2" class="border-b border-black">
                        @if ($customer->equipmentStores->isNotEmpty())
                            @foreach ($customer->equipmentStores as $equipmentStore)
                                {{ $equipmentStore->equipment->serial_no }}
                                @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        @else
                            No Equipment Assigned
                        @endif
                    </td>
                </tr>

                <tr>
                    <td>Remarks of Technician:</td>
                    <td colspan="4" class="border-b border-black" id="technician-remarks-display">&nbsp;</td>
                </tr>
    
                <tr>
                    <td>Document/s Needed (if any):</td>
                    <td colspan="2" class="border-b border-black">&nbsp;</td>
                    <td> </td>
                    <td> </td>
                </tr>
    
                <tr>
                    <td class="whitespace-nowrap">Document/s Submitted (if any):</td>
                    <td colspan="2" class="border-b border-black w-full pl-3"></td>
                    <td> </td>
                    <td class="whitespace-nowrap pr-2">Inspected by:</td>
                    
                  </tr>

                  <tr>
                    <td colspan="3"> </td>

                    <td class="border-b border-black">&nbsp;</td>
                    <td class="border-b border-black pl-4">&nbsp;</td>
                    
                </tr>

                <tr>
                    <td colspan="2"> </td>

                    <td colspan="2" class="text-right">Customer's Name & Signature</td>
                    <td class="text-center">Signature over Printed</td>
                    
                </tr>
                <tr>
                    <td colspan="5" class="border-b border-black border-dashed">&nbsp;</td>
                    
                </tr>
            </table>
    
         <div class="flex justify-end space-x-1 mt-4 action-buttons">
  <!-- Print Button -->
  <button 
    onclick="openRemarksModal()" 
    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200"
  >
    Print
  </button>

  <!-- Cancel Button -->
  <button 
    onclick="window.history.back()" 
    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow transition duration-200"
  >
    Cancel
  </button>
</div>

                
            </div>
      
    </page>
 
<div id="remarksModal" class="modal print-hide">
        <div class="modal-content">
            <h2 class="text-xl font-bold mb-4">Enter Technician Remarks</h2>
            <textarea 
                id="technicianRemarks" 
                class="w-full p-2 border border-gray-300 rounded mb-4 text-5xl" 
                rows="4"
                placeholder="Enter remarks here..."
            ></textarea>
            <div class="flex justify-end space-x-2">
                <button 
                    onclick="closeRemarksModal()" 
                    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow transition duration-200"
                >
                    Cancel
                </button>
                <button 
                    onclick="submitRemarks()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200"
                >
                    Submit and Print
                </button>
            </div>
        </div>
    </div>

    <script>
        function openRemarksModal() {
            document.getElementById('remarksModal').style.display = 'block';
        }

        function closeRemarksModal() {
            document.getElementById('remarksModal').style.display = 'none';
        }

        function submitRemarks() {
            const remarks = document.getElementById('technicianRemarks').value;
            document.getElementById('technician-remarks-display').textContent = remarks;
            closeRemarksModal();
            setTimeout(() => window.print(), 100);
        }
    </script>
</body>
</html>
