@extends('dashboard.layouts.admin-layout')
@section('title', 'Edit Purchase')

@section('content')
<div class="container-fluid">
    <h2 class="mb-3">Edit Purchase</h2>

    <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="supplier_id" class="form-label">Supplier</label>
                <select name="supplier_id" class="form-control" required>
                    <option value="">-- Select Supplier --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" 
                            {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Invoice No</label>
                <input type="text" name="invoice_no" class="form-control" value="{{ old('invoice_no', $purchase->invoice_no) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Purchase Date</label>
                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d')) }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="payment_status" class="form-label">Payment Status</label>
                <select name="payment_status" class="form-control" required>
                    @php
                        $statuses = ['Paid', 'Partial', 'Due'];
                        $oldPaymentStatus = old('payment_status', $purchase->payment_status);
                    @endphp
                    <option value="">-- Select Payment Status --</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" {{ $oldPaymentStatus == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select name="payment_method" class="form-control">
                    @php $oldPaymentMethod = old('payment_method', $purchase->payment_method); @endphp
                    <option value="">-- Select --</option>
                    <option value="Cash" {{ $oldPaymentMethod == 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Bank" {{ $oldPaymentMethod == 'Bank' ? 'selected' : '' }}>Bank</option>
                    <option value="bKash" {{ $oldPaymentMethod == 'bKash' ? 'selected' : '' }}>bKash</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Total Amount</label>
                <input type="number" step="0.01" name="total_amount" id="totalAmount" class="form-control" readonly value="{{ old('total_amount', $purchase->total_amount) }}">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="itemsTable">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Variant (Color + Size)</th>
                        <th>Batch</th>
                        <th>Purchase Price</th>
                        <th>Sale Price</th>
                        <th>Qty</th>
                        <th>Expiry</th>
                        <th>Total</th>
                        <th><button type="button" class="btn btn-sm btn-success" id="addItem">+</button></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    @php
                        $oldItems = old('items', $purchase->purchaseItems->toArray());
                        $productsJson = $products->toJson();
                    @endphp

                    @foreach ($oldItems as $index => $item)
                        <tr>
                            <td>
                                <select name="items[{{ $index }}][product_id]" class="form-control product-select" data-index="{{ $index }}" required>
                                    <option value="">-- Select Product --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ $item['product_id'] == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="items[{{ $index }}][product_variant_id]" class="form-control variant-select" required>
                                    {{-- Options will be loaded by JS --}}
                                </select>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][batch_number]" class="form-control" value="{{ $item['batch_number'] ?? '' }}">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][purchase_price]" class="form-control price" step="0.01" min="0" value="{{ $item['purchase_price'] ?? '' }}" required>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][sale_price]" class="form-control sale-price" step="0.01" min="0" value="{{ $item['sale_price'] ?? '' }}" required>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][quantity]" class="form-control qty" min="1" value="{{ $item['quantity'] ?? 1 }}" required>
                            </td>
                            <td>
                                <input type="date" name="items[{{ $index }}][expiry_date]" class="form-control" value="{{ $item['expiry_date'] ?? '' }}">
                            </td>
                            <td>
                                <input type="number" class="form-control line-total" readonly value="{{ isset($item['purchase_price'], $item['quantity']) ? number_format($item['purchase_price'] * $item['quantity'], 2) : '' }}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger removeItem">&times;</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>Paid Amount</label>
                <input type="number" step="0.01" name="paid_amount" id="paidAmount" class="form-control" required value="{{ old('paid_amount', $purchase->paid_amount) }}">
            </div>
            <div class="col-md-3">
                <label>Due Amount</label>
                <input type="number" step="0.01" name="due_amount" id="dueAmount" class="form-control" readonly value="{{ old('due_amount', $purchase->due_amount) }}">
            </div>
        </div>

        <div class="mb-3">
            <label>Note</label>
            <textarea name="note" class="form-control">{{ old('note', $purchase->note) }}</textarea>
        </div>

        <button class="btn btn-primary">Update Purchase</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = {{ count($oldItems) }};
const products = {!! $productsJson !!};

// Add new row
function addItemRow() {
    const row = `
        <tr>
            <td>
                <select name="items[${itemIndex}][product_id]" class="form-control product-select" data-index="${itemIndex}" required>
                    <option value="">-- Select Product --</option>
                    ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                </select>
            </td>
            <td>
                <select name="items[${itemIndex}][product_variant_id]" class="form-control variant-select" required>
                    <option value="">-- Select Variant --</option>
                </select>
            </td>
            <td>
                <input type="text" name="items[${itemIndex}][batch_number]" class="form-control">
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][purchase_price]" class="form-control price" step="0.01" min="0" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][sale_price]" class="form-control sale-price" step="0.01" min="0" required>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control qty" min="1" value="1" required>
            </td>
            <td>
                <input type="date" name="items[${itemIndex}][expiry_date]" class="form-control">
            </td>
            <td>
                <input type="number" class="form-control line-total" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger removeItem">&times;</button>
            </td>
        </tr>
    `;
    $('#itemsBody').append(row);
    itemIndex++;
}

$('#addItem').on('click', addItemRow);
$(document).on('click', '.removeItem', function () {
    $(this).closest('tr').remove();
    calculateTotals();
});

// Calculate line totals and overall totals
$(document).on('input', '.price, .qty', function () {
    const row = $(this).closest('tr');
    const qty = parseFloat(row.find('.qty').val()) || 0;
    const price = parseFloat(row.find('.price').val()) || 0;
    const total = qty * price;
    row.find('.line-total').val(total.toFixed(2));
    calculateTotals();
});

$('#paidAmount').on('input', calculateTotals);

function calculateTotals() {
    let total = 0;
    $('.line-total').each(function () {
        total += parseFloat($(this).val()) || 0;
    });
    $('#totalAmount').val(total.toFixed(2));

    const paid = parseFloat($('#paidAmount').val()) || 0;
    $('#dueAmount').val((total - paid).toFixed(2));
}

// Load variants for the selected product and populate variant select
function loadVariants(productId, variantSelect, selectedVariantId = null) {
    variantSelect.html('<option>Loading...</option>');
    if (!productId) {
        variantSelect.html('<option value="">-- Select Variant --</option>');
        return;
    }
    $.get(`/admin/products/${productId}/variants`, function(res) {
        let options = '<option value="">-- Select Variant --</option>';
        res.variants.forEach(variant => {
            // Compose variant label: Color + Size or fallback
            let label = '';
            if (variant.color_name) label += variant.color_name;
            if (variant.color_name && variant.size_name) label += ' / ';
            if (variant.size_name) label += variant.size_name;
            if (!label) label = 'Variant #' + variant.id;

            options += `<option value="${variant.id}" ${selectedVariantId == variant.id ? 'selected' : ''}>${label}</option>`;
        });
        variantSelect.html(options);
    });
}

// When product changes, load variants for that row
$(document).on('change', '.product-select', function () {
    const productId = $(this).val();
    const row = $(this).closest('tr');
    const variantSelect = row.find('.variant-select');
    loadVariants(productId, variantSelect);
});

// On page load, for existing rows, load variant options and select current variant
$(document).ready(function () {
    $('#itemsBody tr').each(function(index, tr) {
        const productSelect = $(tr).find('.product-select');
        const variantSelect = $(tr).find('.variant-select');

        const productId = productSelect.val();
        // For existing data, oldItems include 'product_variant_id'?
        let selectedVariantId = null;
        @foreach ($oldItems as $i => $item)
            if (index === {{ $i }}) {
                selectedVariantId = "{{ $item['product_variant_id'] ?? '' }}";
            }
        @endforeach

        if (productId) {
            loadVariants(productId, variantSelect, selectedVariantId);
        }
    });

    calculateTotals();
});
</script>
@endpush
