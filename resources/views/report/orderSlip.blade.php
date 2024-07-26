<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORDER SLIP FORM</title>


    <link rel="stylesheet" href="{{ asset('css/papersizes.css') }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                // set font size small to 8px
                fontSize: {
                    'sm': '8px',
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

<body>
    <page class="text-md" size="letter" layout="landscape">

        <div class="flex mt-5">
            <div class="w-[5%]"></div>
            <div class="w-[20%]">TRUCK NO.</div>
            <div class="w-[50%]">
                Date: <span class="font-bold">{{ $orderSlip->rCreatedAt }}</span><br>
                Delivery Person: <span class="font-bold">{{ $orderSlip->delivery_person }}</span> <br>
            </div>
            <div class="w-[20%]  text-lg">
                TOTAL: <span class="font-bold">{{ formatNumber($grandTotal) }}</span>
            </div>

            <div class="w-[5%]"></div>
        </div>

        <div class="flex w-full h-[75%]">

            <div class="w-[5%]"></div>

            {{-- <div class="grid grid-rows-5 grid-flow-col auto-cols-max auto-rows-max"> --}}
            <div class="flex flex-row flex-wrap h-[75%] justify-start">
                @foreach ($inbounds as $outbound)
                    @php
                        $orderedProducts = json_decode($outbound->products, true);
                    @endphp

                    <div class="w-32">
                        <div class="w-32 border-solid border-2 border-black p-1">
                            <div class="flex">
                                <p class="w-1/2 text-sm">Total</p>
                                <div class="text-sm font-bold">
                                    {{ formatNumber(getTotalOfProducts($orderedProducts)) }}</div>
                            </div>

                            <div class="flex">
                                <div class="w-1/2 text-sm">Sequence No.</div>
                                <div class="text-sm font-bold">{{ $outbound->order_slip_sno }}</div>
                            </div>

                            <div class="flex">
                                <div class="w-1/2 text-sm">Customer:</div>
                                <div class="text-sm font-bold">{{ $outbound->customer_name }}</div>
                            </div>

                            <div class="flex">
                                <div class="w-1/2 text-sm">Degic No:</div>
                                <div class="text-sm font-bold">{{ $outbound->degic_no }}</div>
                            </div>

                            <div class="flex">
                                <div class="w-1/2 text-sm">Product</div>
                                <div class="text-sm">Quantity</div>
                            </div>



                            @foreach ($orderedProducts as $product)
                                <div class="flex">
                                    <div class="w-1/2 text-sm font-bold">{{ $product['code'] }}</div>
                                    <div class="text-sm font-bold">{{ $product['quantity'] }}</div>
                                </div>
                            @endforeach
                        </div>



                        <div class="flex">
                            <div class="text-sm">Total Quantiy for Checking</div>
                        </div>

                        @php
                            $summary = getSummaryOfProducts($orderedProducts);
                        @endphp

                        @foreach ($summary as $key => $value)
                            <div class="flex">
                                <div class="w-1/2 text-sm font-bold">{{ $key }}</div>
                                <div class="text-sm font-bold">{{ $value['total'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="w-[5%]"></div>

        </div>


        <div class="flex">
            <div class="w-[5%]"></div>
            <div class="w-[90%]">
                ENCODED BY: {{ $orderSlip->generated_by }} <br>
                REMARKS: <br>
            </div>
            <div class="w-[5%]"></div>
        </div>

        <div class="flex">
            <div class="w-[5%]"></div>

            <div class="flex w-[30%]">
                <div class="">CHECKER:</div>
                <div class="">____________________________________</div>
            </div>

            <div class="w-[40%]">
                FREE: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ____

                SC
                <input type="checkbox" name="" id="">

                MC
                <input type="checkbox" name="" id="">

                BC
                <input type="checkbox" name="" id="">

                FLVR:______

                PC/S:______
            </div>
            <div class="w-[5%]"></div>
        </div>

        <div class="flex">
            <div class="w-[5%]"></div>


            <div class="flex w-[30%]">
                <div class="">TRUCK CHECKER:</div>
                <div class="">_____________________________</div>
            </div>


            <div class="w-[40%]">
                CHARGE: ____

                SC
                <input type="checkbox" name="" id="">

                MC
                <input type="checkbox" name="" id="">

                BC
                <input type="checkbox" name="" id="">

                FLVR:______

                PC/S:______
            </div>
            <div class="w-[5%]"></div>

        </div>
    </page>

</body>

</html>
