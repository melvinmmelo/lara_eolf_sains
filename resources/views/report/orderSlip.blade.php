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

<body>

    @foreach ($pagesData as $pageNumber => $pageInbounds)
        <page class="text-md" size="legal" layout="landscape">
            <div class="flex mt-5 mb-2">
                <div class="w-[5%]"></div>
                <div class="w-[50%]">
                    Date: <span class="font-bold">{{ $orderSlip->rCreatedAt }}</span><br>
                    Delivery Person/Driver Name: <span class="font-bold">{{ $orderSlip->delivery_person }} /
                        {{ $orderSlip->driver_name }}</span> <br>

                </div>
                <div class="w-250%]">

                </div>
                <div class="w-[20%]  text-lg">
                    TOTAL: <span class="font-bold">{{ formatNumber($grandTotal) }}</span>
                </div>
                <div class="w-[5%]"></div>
            </div>

            <div class="flex w-full h-[75%]">
                <div class="w-[5%]"></div>
                <div class="flex flex-row flex-wrap h-[75%] justify-start">
                    @foreach ($pageInbounds as $inbound)
                        @php
                            $orderedProducts = json_decode($inbound->products, true);
                            usort($orderedProducts, function ($a, $b) {
                                return $a['order'] <=> $b['order'];
                            });
                            $totalProducts = count($orderedProducts);
                        @endphp

                        <div class="w-32">
                            <div class="w-32 border-solid border-2 border-black p-1">
                                <!-- Inbound details -->
                                <div class="flex">
                                    <p class="w-1/2 text-sm">Total</p>
                                    <div class="text-sm font-bold">{{ formatNumber($inbound->totalAmount) }}</div>
                                </div>
                                <div class="flex">
                                    <div class="w-1/2 text-sm">Seq. No.</div>
                                    <div class="text-sm font-bold">{{ $inbound->order_slip_sno }}</div>
                                </div>

                                <div class="flex">
                                    <div class="w-1/2 text-sm">Customer:</div>
                                    <div class="text-sm font-bold">{{ $inbound->customer_name }}</div>
                                </div>

                                <div class="flex">
                                    <div class="w-1/2 text-sm">Degic No:</div>
                                    <div class="text-sm font-bold">{{ $inbound->degic_no }}</div>
                                </div>

                                <div class="flex">
                                    <div class="w-1/2 text-sm">Product</div>
                                    <div class="text-sm">Quantity</div>
                                </div>

                                <!-- Product list -->
                                @foreach (array_slice($orderedProducts, 0, 23) as $product)
                                    <div class="flex">
                                        <div class="w-1/2 text-sm font-bold">{{ $product['code'] }}</div>
                                        <div class="text-sm font-bold">{{ $product['quantity'] }}</div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Summary section -->
                            @if ($totalProducts <= 23)
                                @php $summary = getSummaryOfProducts($orderedProducts); @endphp
                                <div class="text-sm">Total Quantity for Checking </div>
                                @foreach ($summary as $key => $value)
                                    <div class="flex">
                                        <div class="w-1/2 text-sm font-bold">{{ $key }}</div>
                                        <div class="text-sm font-bold">{{ $value['total'] }}</div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Additional columns for products if needed -->
                        @if ($totalProducts > 23)
                            <div class="w-32">
                                <div class="border-r-2 border-l-2 border-b-2 border-black">
                                    @foreach (array_slice($orderedProducts, 23, 23) as $product)
                                        <div class="flex">
                                            <div class="w-1/2 text-sm font-bold">{{ $product['code'] }}</div>
                                            <div class="text-sm font-bold">{{ $product['quantity'] }}</div>
                                        </div>
                                    @endforeach
                                </div>

                                @php $summary = getSummaryOfProducts($orderedProducts); @endphp
                                <div class="text-sm">Total Quantity for Checking </div>
                                @foreach ($summary as $key => $value)
                                    <div class="flex">
                                        <div class="w-1/2 text-sm font-bold">{{ $key }}</div>
                                        <div class="text-sm font-bold">{{ $value['total'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($totalProducts > 46)
                            <div class="w-32">
                                <div class="border-r-2 border-l-2 border-b-2 border-black">
                                    @foreach (array_slice($orderedProducts, 46) as $product)
                                        <div class="flex">
                                            <div class="w-1/2 text-sm font-bold">{{ $product['code'] }}</div>
                                            <div class="text-sm font-bold">{{ $product['quantity'] }}</div>
                                        </div>
                                    @endforeach
                                </div>

                                @php $summary = getSummaryOfProducts($orderedProducts); @endphp
                                <div class="text-sm">Total Quantity for Checking </div>
                                @foreach ($summary as $key => $value)
                                    <div class="flex">
                                        <div class="w-1/2 text-sm font-bold">{{ $key }}</div>
                                        <div class="text-sm font-bold">{{ $value['total'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="w-[5%]"></div>
            </div>

            <!-- Footer section -->
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
    @endforeach

</body>

</html>
