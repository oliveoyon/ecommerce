<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Product Management</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container my-4">
        <h2 class="mb-3">Products</h2>

        <button class="btn btn-primary mb-3" id="addProductBtn">Add Product</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Unit</th>
                    <th>Purchase Price</th>
                    <th>Sale Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                @foreach ($products as $product)
                    <tr id="productRow-{{ $product->id }}">
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->brand->name ?? '' }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>{{ $product->subcategory->name }}</td>
                        <td>{{ $product->unit->name ?? '' }}</td>
                        <td>{{ $product->default_purchase_price }}</td>
                        <td>{{ $product->default_sale_price }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm editProductBtn"
                                data-id="{{ $product->id }}">Edit</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Fullscreen Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <form id="productForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalTitle">Add Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <input type="hidden" id="product_id" name="product_id">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Brand</label>
                                <select name="brand_id" class="form-control">
                                    <option value="">-- Select Brand --</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subcategory</label>
                                <select name="subcategory_id" class="form-control" required>
                                    <option value="">-- Select Subcategory --</option>
                                    {{-- Will load dynamically --}}
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Unit</label>
                                <select name="unit_id" class="form-control">
                                    <option value="">-- Select Unit --</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Purchase Price</label>
                                <input type="number" name="default_purchase_price" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sale Price</label>
                                <input type="number" name="default_sale_price" class="form-control" step="0.01" min="0">
                            </div>

                            <div class="col-md-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="has_variants" value="1" id="has_variants">
                                    <label class="form-check-label" for="has_variants">
                                        Has Variants
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="has_expiry" value="1" id="has_expiry">
                                    <label class="form-check-label" for="has_expiry">
                                        Has Expiry
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Product Images</label>
                                <input type="file" name="images[]" class="form-control" multiple>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="saveProductBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(function () {
            var productModal = new bootstrap.Modal(document.getElementById('productModal'));

            // Show modal for adding product
            $('#addProductBtn').click(function () {
                $('#productForm')[0].reset();
                $('#formMethod').val('POST');
                $('#product_id').val('');
                $('#productModalTitle').text('Add Product');
                $('select[name="subcategory_id"]').html('<option value="">-- Select Subcategory --</option>');
                productModal.show();
            });

            // Load subcategories on category change
            $('select[name="category_id"]').change(function () {
                var categoryId = $(this).val();
                if (!categoryId) {
                    $('select[name="subcategory_id"]').html('<option value="">-- Select Subcategory --</option>');
                    return;
                }

                $.get('/admin/subcategories-by-category/' + categoryId, function (data) {
                    let options = '<option value="">-- Select Subcategory --</option>';
                    data.forEach(function (subcat) {
                        options += `<option value="${subcat.id}">${subcat.name}</option>`;
                    });
                    $('select[name="subcategory_id"]').html(options);
                });
            });

            // Edit product button
            $('.editProductBtn').click(function () {
                var id = $(this).data('id');
                $.get('/admin/products/' + id + '/edit', function (data) {
                    $('#product_id').val(data.id);
                    $('#formMethod').val('PUT');
                    $('#productModalTitle').text('Edit Product');

                    $('[name="name"]').val(data.name);
                    $('[name="brand_id"]').val(data.brand_id);
                    $('[name="category_id"]').val(data.category_id).trigger('change');

                    // Wait for subcategories to load before selecting
                    setTimeout(() => {
                        $('[name="subcategory_id"]').val(data.subcategory_id);
                    }, 300);

                    $('[name="unit_id"]').val(data.unit_id);
                    $('[name="default_purchase_price"]').val(data.default_purchase_price);
                    $('[name="default_sale_price"]').val(data.default_sale_price);
                    $('[name="has_variants"]').prop('checked', data.has_variants);
                    $('[name="has_expiry"]').prop('checked', data.has_expiry);
                    $('[name="description"]').val(data.description);

                    productModal.show();
                });
            });

            // Save form (add or update)
            $('#productForm').submit(function (e) {
                e.preventDefault();
                $('#saveProductBtn').prop('disabled', true);

                let id = $('#product_id').val();
                let method = $('#formMethod').val();
                let url = method === 'POST' ? '/admin/products' : `/admin/products/${id}`;

                let formData = new FormData(this);
                if (method === 'PUT') {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function () {
                        location.reload();
                    },
                    error: function (xhr) {
                        alert('Failed to save product. Please check your input.');
                    },
                    complete: function () {
                        $('#saveProductBtn').prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>

</html>
