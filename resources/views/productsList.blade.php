    <label class="form-label" for="button types">Products</label>
    <br>
    @if (count($products) == 0)
        <p>No products/prices found</p>
    @else
        @foreach ($products as $product)
            @php
                $product = (object) $product;
            @endphp
            <button type="button" class="btn btn-primary d-block mb-2 w-100"
                onclick="addProduct('{{ $product->code }}')">{{ $product->code }}
                ({{ $product->qty }})
            </button>
        @endforeach
    @endif
