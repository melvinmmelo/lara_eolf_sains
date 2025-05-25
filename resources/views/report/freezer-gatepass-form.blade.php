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
            </div>
    
            <table class="table-fixed w-full border border-black border-collapse">
                <tr>
                    
                    <td colspan="4" class="font-bold text-center text-xl border border-black">FREEZER GATEPASS </td>
                    <td colspan="2" class="font-bold text-center border border-black"> NO. 0230 </td>
                </tr>

               <tr>
                <td class="font-bold border border-black text-center"> DATE </td>
                <td colspan="3" class="font-bold border border-black text-center "> ITEMS DESCRIPTION </td>
                <td colspan="2" class="font-bold border border-black"> </td>
               </tr>

               <tr>
                <td class="font-bold border border-black text-center align-top">02/25/2024</td>
                <td colspan="3" class="font-bold border border-black pl-3">
<div class="font-bold">DISTRIBUTOR: JOFREN D. COMIA _ CAGAYAN</div>
<div>GLASS TOP FREEZER</div>
<div class="font-bold">ADQUILEN, JUVY</div>
<div>ZONE 5, NEWAGAC, GATTARAN, CAGAYAN</div>
<div class="mb-5"></div>

<div class="font-bold">MODEL:  EFE-3502</div>
    <div class="font-bold">SERIAL NO: L60.371.651.3</div>
        <div class="font-bold">DEGIC NO: G-0036.022315</div>
            <div class="font-bold">Free Small Cup</div>
                <div class="font-bold">Checker:</div>
                    <div class="font-bold">Loader:</div>
                        <div class="font-bold mb-4">Remarks:</div>
                </td>
                <td colspan="2" class="font-bold border border-black align-top pl-3""> 
                    <div class="font-bold">FODS NO. 1234</div>
                    <div><input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600">
                            <span class="font-bold">Ice Scraper</span></div>
                    <div><input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600">
                                <span class="font-bold">Lock and Key (Half Charge)</span></div>
                                <div><input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600">
                                    <span class="font-bold">Signage w/ Bracket</span></div>
                                    <div><input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600">
                                        <span class="font-bold">Tarpaulin (logo)</span></div>
                                        <div><input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600">
                                            <span class="font-bold">Tarpaulin (Price list)</span></div>
                            

                </td>
               
               </tr>
            </table>

            <table class="table-fixed w-full border-separate border-spacing-y-5 border-spacing-x-5 pt-2">
                <tr>
                    <td></td>
                    <td class="text-right">Total:</td>
                    <td class="border-b border-black"></td>
                </tr>

                <tr>
                    <td class="border-b border-black pt-5"></td>
                    <td class="border-b border-black"></td>
                    <td class="border-b border-black"></td>
                </tr>

                <tr>
                    <td>Requested By:</td>
                    <td>Issued By:</td>
                    <td>Received By:</td>
                </tr>
            </table>
    
            <div class="flex justify-end space-x-1 mt-4">
  <!-- Print Button -->
  <button 
    onclick="window.print()" 
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
</body>
</html>
