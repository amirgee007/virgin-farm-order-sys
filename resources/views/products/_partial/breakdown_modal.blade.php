<div class="modal-header">
    <h5 class="modal-title">Breakdown for Product: {{ $product->product_text }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    @if($linkedProducts->count())
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Product</th>
                <th>Stems</th>
                <th>UOM</th>
                <th>Size</th>
            </tr>
            </thead>
            <tbody>
            @foreach($linkedProducts as $p)
                <tr>
                    <td>{{ $p->product_text }}</td>
                    <td>{{ $p->pivot->stems ?? '—' }}</td>
                    <td>{{ $p->unit_of_measure }}</td>
                    <td>{{ $p->size }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <strong>Total Stems: {{ $linkedProducts->sum(fn($p) => $p->pivot->stems ?? 0) }}</strong>
    @else
        <p>No group products found.</p>
    @endif
</div>

<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
