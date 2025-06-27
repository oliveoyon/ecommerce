@extends('dashboard.layouts.admin-layout')
@section('title', 'Purchase Details')

@section('content')
<div class="container-fluid">
    <h2 class="mb-3">Purchase Details</h2>

    <div class="card mb-4">
        <div class="card-body">
            <h5><strong>Invoice No:</strong> {{ $purchase->invoice_no ?? 'N/A' }}</h5>
            <p><strong>Supplier:</strong> {{ $purchase->supplier->name ?? '-' }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</p>
            <p><strong>Payment Status:</strong> {{ $purchase->payment_status }}</p>
            <p><strong>Payment Method:</strong> {{ $purchase->payment_method ?? 'N/A' }}</p>
            <p><strong>Total:</strong> {{ number_format($purchase->total_amount, 2) }}</p>
            <p><strong>Paid:</strong> {{ number_format($purchase->paid_amount, 2) }}</p>
            <p><strong>Due:</strong> {{ number_format($purchase->due_amount, 2) }}</p>
            <p><strong>Note:</strong> {{ $purchase->note ?? 'N/A' }}</p>
        </div>
    </div>

    <h5>Purchase Items</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Variant</th>
                <th>Batch No</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
                <th>Expiry</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase->purchaseItems as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Unknown' }}</td>
                    <td>
                        @if ($item->product_variant_id)
                            {{ optional($item->productVariant)->name ?? 'Variant #' . $item->product_variant_id }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->batch_number ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->purchase_price, 2) }}</td>
                    <td>{{ number_format($item->total_price, 2) }}</td>
                    <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('d M Y') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('purchases.index') }}" class="btn btn-secondary mt-3">Back to List</a>
</div>
@endsection
