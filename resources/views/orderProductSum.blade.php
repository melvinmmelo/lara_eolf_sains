            <div class="product-list">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Product Type</th>
                            <th>Quantity</th>

                        </tr>
                    </thead>
                    <tbody>

                        @php
                            $totalSpCount = [];
                            $totalSpCountSet = [];
                        @endphp

                        @if (count($summary))
                            @foreach ($summary as $summ)
                                @php
                                    $spCnt = 0;
                                    $spCnt2 = 0;
                                @endphp

                                @if ($summ['ptype_code'] === 'SC')
                                    @php
                                        $spCnt = $summ['total'] * $summ['sppb'];
                                        $spCnt2 = $spCnt / 12;
                                    @endphp
                                @elseif ($summ['ptype_code'] === 'MC')
                                    @php
                                        $spCnt = $summ['total'] * $summ['sppb'];
                                        $spCnt2 = $spCnt / 12;
                                    @endphp
                                @elseif ($summ['ptype_code'] === 'BC')
                                    @php
                                        $spCnt = $summ['total'] * $summ['sppb'];
                                        $spCnt2 = $spCnt / 12;
                                    @endphp
                                @endif

                                @php
                                    $totalSpCount[] = $spCnt;
                                    $totalSpCountSet[] = $spCnt2;
                                @endphp
                                <tr>
                                    <td>
                                        <input type="text" class="label-input"
                                            value="{{ $summ['ptype_code'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="label-input"
                                            value="{{ $summ['total'] }}" readonly>
                                    </td>
                                </tr>
                            @endforeach

                        @endif
                    </tbody>
                </table>
            </div>


            @if (count($summary))
                <div>
                    <label for="spoon_count">Spoon Count</label>
                    <input type="text" name="sum_spoon_count" id="sum_spoon_count" class="form-control w-100"
                        value="{{ number_format(array_sum($totalSpCount)) }}" readonly>

                    <label for="sum_set_spoon">Total</label>
                    <input type="text" name="sum_set_spoon" id="sum_set_spoon" class="form-control w-100"
                        value="{{ number_format(array_sum($totalSpCountSet)) }}" readonly>
                </div>
            @endif
