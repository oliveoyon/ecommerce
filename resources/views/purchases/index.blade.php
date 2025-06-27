@extends('dashboard.layouts.admin-layout')
@section('title', 'Purchases')

@section('content')
<div class="container-fluid">
    <h2 class="mb-3">All Purchases</h2>

    <a href="{{ route('purchases.create') }}" class="btn btn-primary mb-3">Add Purchase</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Invoice No.</th>
                <th>Supplier</th>
                <th>Date</th>
                <th>Payment</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Due</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->invoice_no ?? 'N/A' }}</td>
                    <td>{{ $purchase->supplier->name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</td>
                    <td>{{ $purchase->payment_status }}</td>
                    <td>{{ number_format($purchase->total_amount, 2) }}</td>
                    <td>{{ number_format($purchase->paid_amount, 2) }}</td>
                    <td>{{ number_format($purchase->due_amount, 2) }}</td>
                    <td>
                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this purchase?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No purchases found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $purchases->links() }}
    </div>
</div>
@endsection
