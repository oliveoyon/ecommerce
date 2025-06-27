@extends('dashboard.layouts.admin-layout')
@section('title', 'Add Purchase')

@section('content')
<div class="container-fluid">
    <h2 class="mb-3">Create Purchase</h2>

    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
        @csrf

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="supplier_id" class="form-label">Supplier</label>
                <select name="supplier_id" class="form-control" required>
                    <option value="">-- Select Supplier --</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Invoice No</label>
                <input type="text" name="invoice_no" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Purchase Date</label>
                <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="itemsTable">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Batch</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Expiry</th>
                        <th>Total</th>
                        <th><button type="button" class="btn btn-sm btn-success" id="addItem">+</button></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <!-- JS Will Append Rows -->
                </tbody>
            </table>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label>Total Amount</label>
                <input type="number" step="0.01" name="total_amount" id="totalAmount" class="form-control" readonly>
            </div>
            <div class="col-md-3">
                <label>Paid Amount</label>
                <input type="number" step="0.01" name="paid_amount" id="paidAmount" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Due Amount</label>
                <input type="number" step="0.01" name="due_amount" id="dueAmount" class="form-control" readonly>
            </div>
            <div class="col-md-3">
                <label>Payment Method</label>
                <select name="payment_method" class="form-control">
                    <option value="">-- Select --</option>
                    <option value="Cash">Cash</option>
                    <option value="Bank">Bank</option>
                    <option value="bKash">bKash</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label>Note</label>
            <textarea name="note" class="form-control"></textarea>
        </div>

        <button class="btn btn-primary">Submit Purchase</button>
    </form>
</div>
@endsection
@push('scripts')
<script>
let itemIndex = 0;

const products = @json($products); // preload all products

function addItemRow() {
    const row = `
        <tr>
            <td>
                <select name="items[${itemIndex}][product_id]" class="form-control product-select" data-index="${itemIndex}" required>
                    <option value="">-- Select --</option>
                    ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                </select>
            </td>
            <td>
                <select name="items[${itemIndex}][color_id]" class="form-control color-select" required>
                    <option value="">-</option>
                </select>
            </td>
            <td>
                <select name="items[${itemIndex}][size_id]" class="form-control size-select" required>
                    <option value="">-</option>
                </select>
            </td>
            <td>
                <input type="text" name="items[${itemIndex}][batch_number]" class="form-control">
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][purchase_price]" class="form-control price" step="0.01" min="0" required>
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

// Auto calculation
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

// Load variants via AJAX when a product is selected
$(document).on('change', '.product-select', function () {
    const productId = $(this).val();
    const row = $(this).closest('tr');
    const index = $(this).data('index');

    // Reset selects
    row.find('.color-select').html('<option value="">-</option>');
    row.find('.size-select').html('<option value="">-</option>');

    if (productId) {
        $.get(`/admin/products/${productId}/variants`, function (res) {
            if (res.colors) {
                let colorOptions = '<option value="">-- Select --</option>';
                res.colors.forEach(c => {
                    colorOptions += `<option value="${c.id}">${c.name}</option>`;
                });
                row.find('.color-select').html(colorOptions);
            }

            if (res.sizes) {
                let sizeOptions = '<option value="">-- Select --</option>';
                res.sizes.forEach(s => {
                    sizeOptions += `<option value="${s.id}">${s.name}</option>`;
                });
                row.find('.size-select').html(sizeOptions);
            }
        });
    }
});

// Initialize with one row
$(document).ready(function () {
    addItemRow();
});
</script>
@endpush
