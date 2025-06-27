@extends('dashboard.layouts.admin-layout')
@section('title', 'Purchases')

@section('content')
<div class="container-fluid">
    <h2 class="mb-3">All Purchases</h2>

    {{-- ------- filter bar ------- --}}
    <form method="GET" class="card card-body mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Supplier</label>
                <select name="supplier_id" class="form-control">
                    <option value="">All</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ request('supplier_id')==$sup->id?'selected':'' }}>
                            {{ $sup->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Payment</label>
                <select name="payment_status" class="form-control">
                    @foreach($statuses as $st)
                        <option {{ request('payment_status',$statuses[0])==$st?'selected':'' }}>
                            {{ $st }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>

            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>

            <div class="col-md-3 d-flex">
                <button class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>
    {{-- ------- /filter bar ------- --}}

    <a href="{{ route('purchases.create') }}" class="btn btn-primary mb-3">Add Purchase</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Invoice No.</th><th>Supplier</th><th>Date</th>
            <th>Payment</th><th>Total</th><th>Paid</th><th>Due</th><th>Actions</th>
        </tr>
        </thead>
        <tbody>
            @forelse($purchases as $purchase)
            <tr>
                <td>{{ $purchase->invoice_no ?? 'N/A' }}</td>
                <td>{{ $purchase->supplier->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</td>
                <td>{{ $purchase->payment_status }}</td>
                <td>{{ number_format($purchase->total_amount,2) }}</td>
                <td>{{ number_format($purchase->paid_amount,2) }}</td>
                <td>{{ number_format($purchase->due_amount,2) }}</td>
                <td>
                    <a href="{{ route('purchases.show',$purchase) }}" class="btn btn-info btn-sm">View</a>
                    <a href="{{ route('purchases.edit',$purchase) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('purchases.destroy',$purchase) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Delete this purchase?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
                <tr><td colspan="8" class="text-center">No purchases found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">{{ $purchases->links() }}</div>
</div>
@endsection
