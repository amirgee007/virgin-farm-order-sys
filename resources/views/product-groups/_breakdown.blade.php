<table class="table table-bordered">
    <thead>
    <tr>
        <th>Product</th>
        <th>Stems</th>
    </tr>
    </thead>
    <tbody>
    @foreach($group->products as $product)
        <tr>
{{--            <td>{{ $product->product_text }}</td>--}}
            <td>{{ $product->pivot->product_text_temp }}</td>
            <td>{{ $product->pivot->stems }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p><strong>Total Stems:</strong> {{ $totalStems }}</p>
