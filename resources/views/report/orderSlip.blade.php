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
                    'xxs': '8px',
                    'xs': '9px',
                    'sm': '10px',
                    'md': '12px',
                    'lg': '14px',
                    'xl': '18px',
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
        page {
            display: flex;
            flex-direction: column;
            color: #000;
        }

        @media print {

            /*
             * Legal landscape. Without this Chrome falls back to Letter, scales the
             * 14in-wide page down to fit, and the leftover height pulls the next slip
             * onto the same sheet.
             */
            @page {
                size: 14in 8.5in;
                margin: 0;
            }

            /* one slip page per sheet, with no trailing blank sheet */
            page {
                break-after: page;
                page-break-after: always;
            }

            page:last-of-type {
                break-after: auto;
                page-break-after: auto;
            }

            /* keep the rules visible on paper */
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    @foreach ($pagesData as $pageNumber => $pageInbounds)
        @php
            if ($loop->first) {
                $sequence_no = 1;
            }
        @endphp

        <page class="text-md" size="legal" layout="landscape">

            {{-- ============================ HEADER ============================ --}}
            <div class="flex items-start mt-4 mb-3">
                <div class="w-[2%]"></div>

                <div class="flex border-2 border-black">
                    {{-- sizes to the longest line so the delivery person never wraps --}}
                    <div class="min-w-[2.6in] px-2 py-1 border-r-2 border-black whitespace-nowrap">
                        <div class="leading-relaxed">Date: <span class="font-bold">{{ $orderSlip->rCreatedAt }}</span>
                        </div>
                        <div class="leading-relaxed">Delivery Person:
                            <span class="font-bold">{{ $orderSlip->delivery_person }}@if ($orderSlip->driver_name)
                                    / {{ $orderSlip->driver_name }}
                                @endif</span>
                        </div>
                    </div>
                    <div class="w-[1.3in] px-2 py-1">Plate No:</div>
                </div>

                <div class="flex-1 text-right text-xl pr-[6%]">
                    TOTAL: <span class="font-bold">{{ formatNumber($grandTotal) }}</span>
                </div>

                <div class="w-[2%]"></div>
            </div>

            {{-- ============================ ORDERS ============================ --}}
            <div class="flex flex-1">
                <div class="w-[2%]"></div>

                <div class="w-[96%] grid grid-cols-9 gap-[3px] items-start content-start">
                    @foreach ($pageInbounds as $inbound)
                        @php
                            $orderedProducts = json_decode($inbound->products, true) ?: [];
                            usort($orderedProducts, function ($a, $b) {
                                return $a['order'] <=> $b['order'];
                            });

                            // Total items per product type, rendered on the last line of each type.
                            $summary = getSummaryOfProducts($orderedProducts);
                            $lastLineOfType = [];
                            foreach ($orderedProducts as $index => $product) {
                                $lastLineOfType[$product['ptype_code']] = $index;
                            }

                            // Long orders spill into extra columns inside the same box.
                            $productColumns = array_chunk($orderedProducts, $perColumn, true);
                            $columnSpan = max(1, count($productColumns));
                        @endphp

                        <div class="col-span-{{ $columnSpan }} flex flex-col">

                            <div class="border-2 border-black flex flex-col flex-1">

                                {{-- order no. + customer --}}
                                <div class="flex items-center gap-1 px-1 pt-[2px]">
                                    <div class="text-md font-bold leading-none">{{ $sequence_no++ }}</div>
                                    <div class="flex-1 text-xs font-bold leading-none text-center">
                                        {{ $inbound->customer_name }}
                                    </div>
                                </div>

                                {{-- order details --}}
                                <div class="px-1 pt-[2px]">
                                    <div class="flex text-xs leading-tight">
                                        <div class="w-1/2">Degic No:</div>
                                        <div class="flex-1 font-bold">{{ $inbound->degic_no }}</div>
                                    </div>

                                    <div class="flex text-xs leading-tight">
                                        <div class="w-1/2">Total:</div>
                                        <div class="flex-1 font-bold">{{ formatNumber($inbound->grandTotal) }}</div>
                                    </div>

                                    @if ($inbound->is_with_sf)
                                        <div class="flex text-xs leading-tight">
                                            <div class="w-1/2">DC:</div>
                                            <div class="flex-1 font-bold">{{ formatNumber(1000) }}</div>
                                        </div>
                                    @endif
                                </div>

                                {{-- products --}}
                                <div class="flex flex-1">
                                    @foreach ($productColumns as $columnIndex => $productColumn)
                                        <div
                                            class="flex-1 px-1 pb-[2px] {{ $columnIndex > 0 ? 'border-l border-black' : '' }}">
                                            <div class="flex text-xxs leading-tight pt-1 whitespace-nowrap">
                                                <div class="w-1/2">Product</div>
                                                <div class="flex-1">Qty.</div>
                                                <div class="text-right">Total items</div>
                                            </div>

                                            @php $lastIndexOfColumn = array_key_last($productColumn); @endphp
                                            @foreach ($productColumn as $index => $product)
                                                @php
                                                    $endsType =
                                                        ($lastLineOfType[$product['ptype_code']] ?? null) === $index;
                                                    // no rule under the final line — the box border already closes it
                                                    $rule = $endsType && $index !== $lastIndexOfColumn;
                                                @endphp
                                                <div
                                                    class="flex text-sm font-bold leading-tight {{ $rule ? 'border-b border-gray-400' : '' }}">
                                                    <div class="w-1/2">{{ $product['code'] }}</div>
                                                    <div class="w-1/4">{{ $product['quantity'] }}</div>
                                                    <div class="w-1/4 text-right">
                                                        @if ($endsType)
                                                            ({{ $summary[$product['ptype_code']]['total'] }})
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                            </div>

                            {{-- written in by hand on the road, so it sits outside the box --}}
                            <div class="px-1 pt-[2px] text-xs leading-tight">Loading Time:</div>
                        </div>
                    @endforeach
                </div>

                <div class="w-[2%]"></div>
            </div>

            {{-- ============================ FOOTER ============================ --}}
            <div class="flex mt-3 mb-4">
                <div class="w-[2%]"></div>

                <div class="w-[96%] flex border-2 border-black text-md">
                    <div class="w-[20%] border-r-2 border-black">
                        <div class="px-2 py-1 border-b border-black">
                            Encoded by: <span class="font-bold">{{ $orderSlip->generated_by }}</span>
                        </div>
                        <div class="px-2 py-1 leading-loose">
                            Helper:<br>
                            Loading Personnel:
                        </div>
                    </div>

                    <div class="w-[17%] border-r-2 border-black">
                        <div class="px-2 py-1 border-b border-black">Printed by: <span class="font-bold">{{ $orderSlip->generated_by }}</span></div>
                        <div class="px-2 py-1 leading-loose">Checker:</div>
                    </div>

                    <div class="w-[13%] border-r-2 border-black px-2 py-1 leading-loose">
                        Loading Time:<br>
                        Start:<br>
                        End:
                    </div>

                    <div class="flex-1 px-2 py-1 flex flex-col">
                        <div>Remarks:</div>
                        <div class="mt-auto text-right text-sm">Page {{ $pageNumber }} of {{ $totalPages }}</div>
                    </div>
                </div>

                <div class="w-[2%]"></div>
            </div>

        </page>
    @endforeach

</body>

</html>
