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
                <div class="font-bold mt-2">PULL-OUT/REPLACED</div>
            </div>
    
            <table class="table-fixed w-full border-separate border-spacing-y-2 border-spacing-x-2">
                <tr>
                    <td colspan="3"></td>
                    <td class="text-right pr-3 font-bold">POF NO:</td>
                    <td class="border-b border-black">----</td>
                </tr>
                
                <tr>
                    <td>Name of Customer:</td>
                    <td colspan="2" class="border-b border-black">{{ $customer->fullName }}</td>
                    <td class="text-right pr-3">Date:</td>
                    <td class="border-b border-black">{{ $date }}</td>
                </tr>
    
                <tr>
                    <td>Current Address on file:</td>
                    <td colspan="4" class="border-b border-black">{{ $customer->storeinfo-> region }}, {{ $customer->storeinfo->province }}, {{ $customer->storeinfo->city }} {{ $customer->storeinfo->brgy }}</td>
                </tr>
    
                <tr>
                    <td></td>
                    <td class="font-bold">PULL-OUT</td>
                    <td></td>
                    <td class="font-bold">REPLACED</td>
                    <td class="font-bold text-center">FREEZER STATUS</td>
                </tr>

                <tr>
                    <td>MODEL/SERIAL NO.</td>
                    <td class="border-b border-black" id="pulloutModelSerialNo">{{ $equipmentStore->equipment->model }} / {{ $equipmentStore->equipment->serial_no }}</td>
                    <td class="pl-2">MODEL/SERIAL NO.</td>
                    <td class="border-b border-black" id="replacedModelSerialNo">{{ $equipmentStore->equipment->model }} / {{ $equipmentStore->equipment->serial_no }}</td>
                    
                    <td class="pl-1">
                        <input type="checkbox" class="form-checkbox h-3 w-3 text-blue-600">
                        <span class="text-sm">DEFECTIVE COMPRESSOR</span>
                    </td>
                </tr>
    
                <tr>
                    <td>DEGIC NO.</td>
                    <td class="border-b border-black" id="pulloutDegicNo">{{ $equipmentStore->equipment->code }}</td>
                    <td class="pl-2">DEGIC NO.</td>
                    <td class="border-b border-black" id="replacedDegicNo">{{ $equipmentStore->equipment->code }}</td>
                    <td class="pl-1">
                        <input type="checkbox" class="form-checkbox h-3 w-3 text-blue-600">
                        <span class="text-sm">NOT COOLING</span>
                    </td>
                </tr>
    
                <tr>
                    <td>PR NO.</td>
                    <td class="border-b border-black"> </td>
                    <td class="pl-2">LOCK & KEY</td>
                    <td class="border-b border-black"> </td>
                    <td class="pl-1">
                        <input type="checkbox" class="form-checkbox h-3 w-3 text-blue-600">
                        <span class="text-sm">STOP SELLING</span>
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>&nbsp;</td>
                    <td class="pl-2">CONDEMNED</td>
                    <td class="border-b border-black"> </td>
                    <td class="pl-1">
                        <input type="checkbox" class="form-checkbox h-3 w-3 text-blue-600">
                        <span class="text-sm">SYSTEM LEAK</span>
                    </td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="pl-2">SIGNAGE</td>
                    <td class="border-b border-black"> </td>
                    <td class="pl-1">
                        <input type="checkbox" class="form-checkbox h-3 w-3 text-blue-600">
                        <span class="text-sm">RETURN TO SUPPLIER</span>
                    </td>
                </tr>

                <tr>
                    <td>Remarks</td>
                    <td colspan="4" class="border-b border-black">N/A</td>
                </tr>
    
                <tr>
                    <td> </td>
                    <td colspan="4" class="border-b border-black">&nbsp;</td>
                </tr>

                  <tr>
                    <td>Prepared By:</td>

                    <td  colspan="2">Noted By:</td>
                    
                    <td>Pull-Out By:</td>
                    <td>Costumer and/or Representative Signature</td>
                    
                </tr>

                <tr>
                    <td class="border-b border-black">
                        @if(session('branch_code') == 'EFTO-CAG')
                            MARY JANE ESTEBAN
                        @else
                            MERZELLE URBANO
                        @endif
                    </td>

                    <td colspan="2" class="border-b border-black">NALEN COMIA</td>
                    
                    <td class="border-b border-black"> </td>
                    <td class="border-b border-black"></td>
    
                </tr>

               
                <tr>
                    <td colspan="5" class="border-b border-black border-dashed">&nbsp;</td>
                    
                </tr>
            </table>
    
            <div class="flex justify-end space-x-1 mt-4">
                <button 
                    onclick="toggleDetails('pullout')"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow transition duration-200"
                >
                    Show Pull-Out Details
                </button>

                <button 
                    onclick="toggleDetails('replaced')"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow transition duration-200"
                >
                    Show Replaced Details
                </button>
                
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

            <!-- Remarks Modal -->
            <div id="remarksModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Enter Remarks</h3>
                        <textarea 
                            id="remarksInput" 
                            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            rows="4"
                            placeholder="Enter your remarks here..."
                        ></textarea>
                        <div class="flex justify-end space-x-2 mt-4">
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
                                Submit & Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
                
            </div>
      
    </page>
    <script>
        function openRemarksModal() {
            document.getElementById('remarksModal').classList.remove('hidden');
        }

        function closeRemarksModal() {
            document.getElementById('remarksModal').classList.add('hidden');
        }

        function submitRemarks() {
            const remarks = document.getElementById('remarksInput').value;
            // Update the remarks in the form
            const remarksRow = Array.from(document.querySelectorAll('tr')).find(row => 
                row.querySelector('td')?.textContent.trim() === 'Remarks'
            );
            if (remarksRow && remarks.trim() !== '') {
                const remarksCell = remarksRow.querySelector('td[colspan="4"].border-b.border-black');
                if (remarksCell) {
                    remarksCell.textContent = remarks;
                }
            }
            
            // Close modal and print
            closeRemarksModal();
            setTimeout(() => {
                window.print();
            }, 100);
        }

        // Close modal if clicking outside
        document.getElementById('remarksModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRemarksModal();
            }
        });

        function toggleDetails(type) {
            const pulloutModelSerialNo = document.getElementById('pulloutModelSerialNo');
            const pulloutDegicNo = document.getElementById('pulloutDegicNo');
            const replacedModelSerialNo = document.getElementById('replacedModelSerialNo');
            const replacedDegicNo = document.getElementById('replacedDegicNo');

            if (type === 'pullout') {
                pulloutModelSerialNo.textContent = "{{ $equipmentStore->equipment->model }} / {{ $equipmentStore->equipment->serial_no }}";
                pulloutDegicNo.textContent = "{{ $equipmentStore->equipment->code }}";
                replacedModelSerialNo.textContent = '';
                replacedDegicNo.textContent = '';
            } else if (type === 'replaced') {
                replacedModelSerialNo.textContent = "{{ $equipmentStore->equipment->model }} / {{ $equipmentStore->equipment->serial_no }}";
                replacedDegicNo.textContent = "{{ $equipmentStore->equipment->code }}";
                pulloutModelSerialNo.textContent = '';
                pulloutDegicNo.textContent = '';
            }
            
        }
    </script>
</body>
</html>
