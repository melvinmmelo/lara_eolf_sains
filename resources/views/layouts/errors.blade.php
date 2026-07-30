@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-primary">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

{{-- @if (session('updatingDataResults'))
    <div class="alert alert-default">
        @php $results = session('updatingDataResults'); @endphp
        @foreach ($results as $result)
            <ul>
                <li>{{ $result['code'] }} {{ $result['message'] }}</li>
            </ul>
        @endforeach
    </div>

    @php session()->forget('updatingDataResults'); @endphp
@endif --}}
