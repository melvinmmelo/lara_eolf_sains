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
                    <td class="border-b border-black">02091</td>
                </tr>
                
                <tr>
                    <td>Name of Customer:</td>
                    <td colspan="2" class="border-b border-black">JOFREN D. COMIA - CAGAYAN</td>
                    <td class="text-right pr-3">Date:</td>
                    <td class="border-b border-black">March 03, 2025</td>
                </tr>
    
    
                <tr>
                    <td>Current Address on file:</td>
                    <td colspan="4" class="border-b border-black">Pagkakaisa St., MALLIG, ISABELA</td>
                </tr>
    
                <tr>
                    <td>Sales Agent:</td>
                    <td colspan="2" class="border-b border-black">INITIAL DELIVERY</td>
                    <td colspan="2"></td>
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
                    <td class="border-b border-black">&nbsp;</td>
                    <td class="pl-2">MODEL/SERIAL NO.</td>
                    <td class="border-b border-black">EFE-4602</td>
                    
                    <td class="pl-1">
                        <input type="checkbox" class="form-checkbox h-3 w-3 text-blue-600">
                        <span class="text-sm">DEFECTIVE COMPRESSOR</span>
                    </td>
                </tr>
    
                <tr>
                    <td>DEGIC NO.</td>
                    <td class="border-b border-black">8318.2021</td>
                    <td class="pl-2">DEGIC NO.</td>
                    <td class="border-b border-black">L61.225.815.8</td>
                    <td class="pl-1">
                        <input type="checkbox" class="form-checkbox h-3 w-3 text-blue-600">
                        <span class="text-sm">NOT COOLING</span>
                    </td>
                </tr>
    
                <tr>
                    <td>PR NO.</td>
                    <td class="border-b border-black"> </td>
                    <td class="pl-2">AGREEMENT NO.</td>
                    <td class="border-b border-black"> </td>
                    <td class="pl-1">
                        <input type="checkbox" class="form-checkbox h-3 w-3 text-blue-600">
                        <span class="text-sm">STOP SELLING</span>
                    </td>
                </tr>

                <tr>
                    <td>CV NO.</td>
                    <td class="border-b border-black"> </td>
                    <td class="pl-2">FODS NO.</td>
                    <td class="border-b border-black"> </td>
                    <td class="pl-1">
                        <input type="checkbox" class="form-checkbox h-3 w-3 text-blue-600">
                        <span class="text-sm">SYSTEM LEAK</span>
                    </td>
                </tr>

                <tr>
                    <td>RS NO.</td>
                    <td class="border-b border-black"> </td>
                    <td class="pl-2">LOCK & KEY</td>
                    <td class="border-b border-black"> </td>
                    <td class="pl-1">
                        <input type="checkbox" class="form-checkbox h-3 w-3 text-blue-600">
                        <span class="text-sm">CONDEMNED</span>
                    </td>
                </tr>

                <tr>
                    <td>REFUND DEPOSIT</td>
                    <td class="border-b border-black"> </td>
                    <td class="pl-2">SIGNAGE</td>
                    <td class="border-b border-black"> </td>
                    <td class="pl-1">
                        <input type="checkbox" class="form-checkbox h-3 w-3 text-blue-600">
                        <span class="text-sm">RETURN TO SUPPLIER</span>
                    </td>
                </tr>

                <tr>
                    <td>Remarks</td>
                    <td colspan="4" class="border-b border-black">CHECK UP FREEZER</td>
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
                    <td class="border-b border-black">Comia Nalen</td>

                    <td colspan="2" class="border-b border-black">Flores, Danilo F/ Flores, Dan Eric</td>
                    
                    <td class="border-b border-black"> </td>
                    <td class="border-b border-black"></td>
    
                </tr>

               
                <tr>
                    <td colspan="5" class="border-b border-black border-dashed">&nbsp;</td>
                    
                </tr>
            </table>
    
          
                
            </div>
      
    </page>
</body>
</html>
