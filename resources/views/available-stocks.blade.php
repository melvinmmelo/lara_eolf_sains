@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Available Stocks</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Available Stocks</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        @php
                            $stockText = '';
                            foreach ($products as $productType => $items) {
                                $stockText .= strtoupper($productType) . "\n";
                                foreach ($items as $product) {
                                    if ($product->available_stocks > 0) {
                                        $stockText .=
                                            $product->product_code .
                                            ' - ' .
                                            $product->variant_name .
                                            ' - ' .
                                            $product->available_stocks .
                                            "\n";
                                    }
                                }
                                $stockText .= "\n";
                            }
                        @endphp

                        <div class="form-group">
                            <textarea class="form-control" rows="20" id="stocksList" readonly>{{ $stockText }}</textarea>
                        </div>
                        <button class="btn btn-primary" onclick="copyFullText()">
                            Copy All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('custom_js')
    <script>
        function copyFullText() {
            const textarea = document.getElementById('stocksList');
            textarea.select();
            document.execCommand('copy');

            // Show feedback
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            setTimeout(() => {
                button.textContent = originalText;
            }, 1000);
        }
    </script>
@endsection
